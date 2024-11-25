#!/usr/bin/env python
# coding: utf-8

from sqlite3 import Cursor
from sqlite3.dbapi2 import _CursorT
import requests
from bs4 import BeautifulSoup
import mysql.connector
import datetime
import re
import time

start_time = time.time()

def connect_to_database():
    return mysql.connector.connect(
        host="localhost",
        username="monito29_admin",
        password="monito29_admin",
        database="monito29_db",
        charset="utf8"
    )

def execute_query(cursor, query, data=None):
    if data:
        cursor.execute(query, data)
    else:
        cursor.execute(query)
    cursor.connection.commit()

def insert_journal(cursor, journal_data):
    insert_query = """
        INSERT INTO `jurnal` (`id_jurnal`, `nama_jurnal`, `link_jurnal`, `jadwal_terbit`, `archive_jurnal`, `volume_terbaru`, `link_indeks`, `indeks_jurnal`)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s);
    """
    execute_query(cursor, insert_query, journal_data)

def delete_duplicates(cursor):
    delete_query = """
        DELETE a
        FROM artikel AS a
        INNER JOIN (
            SELECT link_artikel, MIN(id_artikel) AS min_id
            FROM artikel
            GROUP BY link_artikel
        ) AS b ON a.link_artikel = b.link_artikel AND a.id_artikel <> b.min_id
    """
    execute_query(cursor, delete_query)

def fetch_latest_volume(cursor, base_url):
    update_query = """
        UPDATE jurnal
        SET volume_terbaru = %s
        WHERE archive_jurnal = %s
    """
    execute_query(cursor, update_query, (latest_volume, base_url))

def scrape_articles(soup, archive_jurnal_id, formatted_date):
    articles = []
    artikel_parent = soup.find_all("td", class_="tocArticleTitleAuthors")
    for detail in artikel_parent:
        title_element = detail.find("div", class_="tocTitle")
        title_link = title_element.find("a") if title_element else None
        nama_artikel = title_link.text.strip() if title_link else "Front Matter"
        link_artikel = title_link["href"] if title_link else "#"
        
        authors = [author.text.strip() for author in detail.find_all("div", class_="tocAuthors")]
        penulis_artikel = "\n".join(authors) if authors else "Unknown"

        articles.append((nama_artikel, archive_jurnal_id, link_artikel, formatted_date, penulis_artikel))
    return articles

def insert_articles(cursor, articles):
    insert_query = """
        INSERT INTO artikel (nama_artikel, asal_artikel, link_artikel, tanggal_terbit, penulis_artikel)
        VALUES (%s, %s, %s, %s, %s)
    """
    for article in articles:
        execute_query(cursor, insert_query, article)

def scrape_journal(base_url, archive_jurnal_id):
    page = requests.get(base_url)
    soup = BeautifulSoup(page.content, "html.parser")
    issue_element = soup.find("div", style="clear:left;")
    issue_link = issue_element.find("a")["href"]
    latest_volume = issue_element.find("a").text.strip()

    fetch_latest_volume(Cursor, base_url)

    try:
        page_article = requests.get(issue_link)
        soup2 = BeautifulSoup(page_article.content, "html.parser")
        current_date = datetime.date.today().strftime('%Y-%m-%d')

        articles = scrape_articles(soup2, archive_jurnal_id, current_date)
        insert_articles(_CursorT, articles)

        print(f"Number of articles in this issue: {len(articles)}")
    except requests.exceptions.RequestException as e:
        print(f"Error fetching issue link: {e}")

def main():
    db = connect_to_database()
    cursor = db.cursor()

    cursor.execute("DELETE FROM jurnal WHERE archive_jurnal IN ('https://journal.walisongo.ac.id/index.php/PJIS/issue/archive', 'https://journal.walisongo.ac.id/index.php/rjtpd/issue/archive')")
    delete_duplicates(cursor)

    cursor.execute("SELECT archive_jurnal FROM jurnal")
    archive_links = [x[0] for x in cursor.fetchall()]

    for base_url in archive_links:
        cursor.execute("SELECT id_jurnal FROM jurnal WHERE archive_jurnal = %s", (base_url,))
        archive_jurnal_id = cursor.fetchone()[0]
        scrape_journal(base_url, archive_jurnal_id)

    delete_duplicates(cursor)
    print("Duplicate rows deleted from artikel table.")

    db.close()
    print("Execution time:", time.time() - start_time)

if __name__ == "__main__":
    main()

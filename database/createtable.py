import sqlite3
conn = sqlite3.connect('./Personal.sqlite3')
conn = sqlite3.connect('./Menu.sqlite3')

cur = conn.cursor()
#cur.execute("create table Deal(ID text,name text,category text,price int,point int,stock int,spicy int)")
#cur.execute("create table Manager(user text,password text,vip int,manager int,point int)")

conn.commit()
conn.close()
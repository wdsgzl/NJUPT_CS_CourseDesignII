import sqlite3
#conn = sqlite3.connect('./Menu.sqlite3')
conn = sqlite3.connect('./Personal.sqlite3')

cur = conn.cursor()
#cur.execute("create table Menu(Name text,info text,normal_cost float,vip_cost float,score_cost int)")
#cur.execute("create table Deal(ID text,name text,price int,point int,stock int,spicy int)")
# cur.execute("insert into Deal Values(0,'宫保鸡丁','热菜',38,3000,10,2)")
# cur.execute("insert into Deal Values(1,'鱼香肉丝','热菜',32,2500,15,1)")
# cur.execute("insert into Deal Values(2,'水煮牛肉','热菜',48,4000,10,3)")
# cur.execute("insert into Deal Values(3,'凉拌黄瓜','凉菜',18,1500,25,0)")
# cur.execute("insert into Deal Values(4,'米饭','主食',3,300,100,0)")
# cur.execute("insert into Deal Values(5,'鲜榨西瓜汁','饮品',10,800,10,0)")
# cur.execute("insert into Deal Values(6,'鲜榨橘子汁','饮品',10,800,50,0)")
# cur.execute("insert into Deal Values(7,'葱油拌面','主食',8,600,40,0)")
# cur.execute("insert into Deal Values(8,'肉夹馍','主食',10,1000,100,1)")
# cur.execute("insert into Deal Values(9,'红烧排骨','热菜',48,4800,25,0)")
# cur.execute("insert into Deal Values(10,'毛血旺','热菜',88,8800,10,3)")
# cur.execute("insert into Deal Values(11,'凉拌木耳','凉菜',16,1600,20,1)")
# cur.execute("insert into Deal Values(12,'干锅花菜','热菜',28,2800,10,1)")
# cur.execute("insert into Deal Values(13,'麻婆豆腐','热菜',16,1600,15,2)")
# cur.execute("insert into Deal Values(14,'皮蛋拌豆腐','凉菜',16,1600,15,0)")
# cur.execute("insert into Deal Values(15,'孜然炒牛肉','热菜',20,2000,10,2)")
# cur.execute("insert into Deal Values(16,'金陵烤鸭','热菜',38,3800,25,0)")
# cur.execute("insert into Deal Values(17,'拔丝地瓜','热菜',16,1600,16,0)")
# cur.execute("insert into Deal Values(18,'猪肉炖粉条','热菜',48,4800,10,1)")
# cur.execute("insert into Deal Values(19,'水晶肴肉','凉菜',20,2000,60,0)")
# cur.execute("insert into Deal Values(20,'水果拼盘','凉菜',20,2000,20,0)")







# cur.execute("insert into Manager Values('wangke','21030404',0,1,0)")
# cur.execute("insert into Manager Values('shenwei','21030420',0,1,0)")
# cur.execute("insert into Manager Values('yangwenbo','21030427',0,1,0)")
# cur.execute("insert into Manager Values('panpengyu','21030418',1,0,5000)")
# cur.execute("insert into Manager Values('daixinjie','21030401',0,0,6000)")
# cur.execute("insert into Manager Values('huxiao','21030402',0,0,6600)")
# cur.execute("insert into Manager Values('wenboyang','21030424',1,0,8000)")
# cur.execute("insert into Manager Values('jinchenyang','21030428',0,0,9000)")
cur.execute("update Manager set point=9000 where user= 'panpengyu'")



#print(cur.execute("select Name from Menu"))
#cur.execute("delete from Menu where Name='宫保鸡丁'")

cur.execute("select * from Manager")  #对T_fish表执行数据查找命令
for Menu_row in cur.fetchall():      #以一条记录为元组单位返回结果给row
     print(Menu_row)

# cur.execute("select * from Manager")  #对T_fish表执行数据查找命令
# for Manager_row in cur.fetchall():      #以一条记录为元组单位返回结果给row
#      print(Manager_row)


conn.commit()
conn.close()
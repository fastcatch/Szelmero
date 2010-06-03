insert into cel_tabla c
select * from temp_tabla t
where t.at not in (select at from cel_tabla2);
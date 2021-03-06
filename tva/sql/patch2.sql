begin;
create table tva_belge.version (
	id serial primary key,
	vdate timestamp default now(),
	vdesc text
);

CREATE TABLE tva_belge.parameter_chld
(
  pi_id bigserial NOT NULL, -- PK
  pcode text, -- FK to parameter
  tva_id bigint, -- FK to public.tva_rate
  pcm_val account_type, -- FK to tmp_pcmn
  CONSTRAINT parameter_chld_pkey PRIMARY KEY (pi_id ),
  CONSTRAINT parameter_chld_tva_id_fkey FOREIGN KEY (tva_id)
      REFERENCES tva_rate (tva_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
);

COMMENT ON TABLE tva_belge.parameter_chld  IS 'Child table for parameters (TVA Plugin)';
COMMENT ON COLUMN tva_belge.parameter_chld.pi_id IS 'PK';
COMMENT ON COLUMN tva_belge.parameter_chld.pcode IS 'FK to parameter';
COMMENT ON COLUMN tva_belge.parameter_chld.tva_id IS 'FK to public.tva_rate';
COMMENT ON COLUMN tva_belge.parameter_chld.pcm_val IS 'FK to tmp_pcmn';


CREATE OR REPLACE FUNCTION tva_belge.fill_parameter_chld()
  RETURNS void AS
$BODY$

declare
   a_account text[];
   a_tva_id text[];
   i record;
   e record;
   f record;
   n_size_tva int;
   n_size_account int;
   n_exist_tva int;
begin

for i in select pcode,pvalue,paccount from tva_belge.parameter
loop
	if length(trim(i.pvalue)) = 0 or length(trim(i.paccount)) = 0 then
		continue;
	end if;

	a_account := string_to_array(i.paccount, ',');
	a_tva_id  := string_to_array(i.pvalue,',');

	n_size_tva := array_upper(a_tva_id,1);
	n_size_account := array_upper(a_account,1);


	while n_size_tva <> 0 loop

		while n_size_account <> 0 loop

			select count(*) into n_exist_tva from tva_rate where tva_id=a_tva_id[n_size_tva]::numeric;

			if n_exist_tva != 0 then
				
					insert into tva_belge.parameter_chld (pcode,tva_id,pcm_val)
						values (i.pcode,a_tva_id[n_size_tva]::numeric,a_account[n_size_account]::account_type);
			end if;
			n_size_account := n_size_account -1;
		end loop;
		n_size_account := array_upper(a_account,1);
		n_size_tva := n_size_tva -1;
	end loop;

end loop;

return;

end;

$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION tva_belge.fill_parameter_chld_assujetti()
  RETURNS void AS
$BODY$

declare
   a_account text[];
   a_tva_id text[];
   i record;
   e record;
   f record;
   n_size_tva int;
   n_size_account int;
   n_exist_tva int;
begin

for i in select distinct pvalue from tva_belge.parameter WHERE pcode in ('GRIL00','GRIL01','GRIL02','GRIL03')
loop
	if length(trim(i.pvalue)) = 0  then
		continue;
	end if;

	a_tva_id  := string_to_array(i.pvalue,',');

	n_size_tva := array_upper(a_tva_id,1);


	while n_size_tva <> 0 loop
		select count(*) into n_exist_tva from tva_rate where tva_id=a_tva_id[n_size_tva]::numeric;

		if n_exist_tva != 0 then
			insert into tva_belge.parameter_chld (pcode,tva_id)
				values ('ASSUJETTI',a_tva_id[n_size_tva]::numeric);
		end if;
		n_size_tva := n_size_tva -1;
	end loop;

end loop;

return;

end;

$BODY$
LANGUAGE plpgsql;

select tva_belge.fill_parameter_chld();
select tva_belge.fill_parameter_chld_assujetti();
insert into tva_belge.parameter_chld (pcode,pcm_val) select pcode,paccount from tva_belge.parameter where pcode in ('ATVA','CRTVA','DTTVA');
drop function tva_belge.fill_parameter_chld();
drop function tva_belge.fill_parameter_chld_assujetti();
 alter table tva_belge.parameter drop column paccount;
 alter table tva_belge.parameter drop column pvalue;
insert into tva_belge.version(vdesc) values ('Version 2 : amélioration plugin');
commit;

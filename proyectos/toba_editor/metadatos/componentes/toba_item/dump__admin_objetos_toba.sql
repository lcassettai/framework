------------------------------------------------------------
--[/admin/objetos_toba]--  Componentes 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, creacion) VALUES (
	'1213', --item_id
	'toba_editor', --proyecto
	'/admin/objetos_toba', --item
	NULL, --padre_id
	'toba_editor', --padre_proyecto
	'__raiz__', --padre
	'1', --carpeta
	'0', --nivel_acceso
	NULL, --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'NO', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'generico', --actividad_patron
	'Componentes', --nombre
	NULL, --descripcion
	NULL, --actividad_accion
	'1', --menu
	'2', --orden
	NULL, --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	NULL, --solicitud_registrar_cron
	NULL, --prueba_directorios
	NULL, --zona_proyecto
	NULL, --zona
	NULL, --zona_orden
	NULL, --zona_listar
	'proyecto', --imagen_recurso_origen
	NULL, --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --publico
	NULL, --redirecciona
	NULL, --usuario
	'2005-07-18 01:24:53'  --creacion
);
--- FIN Grupo de desarrollo 0
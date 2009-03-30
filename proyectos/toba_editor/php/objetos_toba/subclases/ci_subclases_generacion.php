<?php 

/**
 * Esta subclase puede trabajar de dos maneras:
 *  - Esclava de un wizard que edita la sublcases de componentes
 *  - Usada directamente como visor/editor php
 */
class ci_subclases_generacion extends toba_ci
{
	protected $s__datos_opciones;	
	protected $s__datos_metodos;
	protected $s__path_archivo;
	protected $s__es_esclavo;
	protected $previsualizacion;
	protected $info_archivo;
	protected $comando_svn;

	function ini()
	{
		$archivo = toba::memoria()->get_parametro('archivo');
		if (isset($archivo)) {
			if (strpos($archivo, '..') !== FALSE) {
				//Evita que se pasen ../ en la url
				throw new toba_error_seguridad("El par�metro '$archivo' no es un path v�lido");
			}
			//Es el ci principal, el parametro que viene es relativo a la carpeta php del proyecto
			$path_php = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php/';
			$this->s__path_archivo = $path_php.$archivo;
			//$this->s__path_archivo = $this->controlador()->get_path_archivo();
			$this->s__es_esclavo = false;
		}

		if (! isset($this->s__es_esclavo)) {
			//Es un esclavo
			$this->s__path_archivo = $this->controlador()->get_path_archivo();
			$this->s__es_esclavo = true;
		}
	}

	function conf()
	{
		$metodos = $this->get_metodos_a_generar();
		$archivo_php = new toba_archivo_php($this->s__path_archivo);
		
		//-- Se va a modificar algo?		
		if (! empty($metodos) || $archivo_php->esta_vacio()) {
			$this->pantalla()->tab('pant_vista_previa')->set_etiqueta('Vista Previa');
		}

		//-- Puede generar c�digo?
		if (! $this->s__es_esclavo) {
			$this->pantalla()->eliminar_tab('pant_opciones');
		}
	}	
	
	function conf__pant_opciones()
	{
		$efs = $this->dep('form_metodos')->get_nombres_ef();
		if (empty($efs)) {
			$this->pantalla()->eliminar_dep('form_metodos');
			$this->pantalla()->eliminar_dep('form_opciones');
			$this->pantalla()->set_descripcion('No hay m�todos nuevos para generar en la subclase');
		}
	}
	
	//-----------------------------------------------------------------
	//---------- OPCIONES 
	//-----------------------------------------------------------------	
	function conf__form_opciones(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_opciones)) {
			$form->set_datos($this->s__datos_opciones);
		}
	}
	
	function evt__form_opciones__modificacion($datos)
	{
		$this->s__datos_opciones = $datos;
	}
	
	function get_opciones()
	{
		return $this->s__datos_opciones;		
	}
	
	//-----------------------------------------------------------------
	//---------- METODOS 
	//-----------------------------------------------------------------
	
	function conf__form_metodos(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_metodos)) {
			$form->set_datos($this->s__datos_metodos);
		}
	}
	
	function evt__form_metodos__modificacion($datos)
	{
		$this->s__datos_metodos = $datos;
	}
	
	//-----------------------------------------------------------------
	//---------- VISTA PREVIA 
	//-----------------------------------------------------------------	
	
	function evt__eliminar()
	{
		unlink($this->s__path_archivo);
	}	


	function evt__svn_blame()
	{
		$this->comando_svn = 'blame';
	}
	
	function evt__svn_revert()
	{
		$this->comando_svn = 'revert';
	}	
	
	function evt__svn_diff()
	{
		$this->comando_svn = 'diff';
	}	
	
	function evt__svn_add()
	{
		$this->comando_svn = 'add';
	}		
	
	function get_previsualizacion()
	{
		return $this->previsualizacion;	
	}	
	
	function get_info_archivo()
	{
		return $this->info_archivo;
	}
	
	function conf__pant_vista_previa()
	{
		$path = $this->s__path_archivo;
		$svn = new toba_svn();		
		if (! isset($this->comando_svn)) {
			$codigo = $this->get_codigo_vista_previa();
		} else {
			switch ($this->comando_svn) {
				case 'blame':
					$codigo = $svn->blame($path);
					break;
				case 'diff':
					$codigo = $svn->diff($path);
					break;			
				case 'revert':
					$svn->revert($path);
					$codigo = $this->get_codigo_vista_previa();
					break;				
				case 'add':
					$svn->add($path);
					$codigo = $this->get_codigo_vista_previa();
					break;									
			}
		}

		//-- Info del archivo
		$estado = $svn->get_estado($path);
		$svn_blame = true;
		$svn_diff = false;
		$svn_add = false;
		switch ($estado) {
			case 'unversioned':
				$svn_blame = false;
				$svn_add = true;
				$nombre = 'sin versionar';
				$img = toba_recurso::imagen_proyecto('svn/unversioned.png', true, 16, 16, $nombre);
				break;
			case 'modified':
			case 'missing':
				$svn_diff = true;
				$nombre = 'modificado';
				$img = toba_recurso::imagen_proyecto('svn/modified.png', true, 16, 16, $nombre);
				break;
			case 'added':
				$svn_blame = false;
				$nombre = 'agregado';
				$img = toba_recurso::imagen_proyecto('svn/added.png', true, 16, 16, $nombre);
				break;
			case 'normal':
				$nombre = 'sin modificar';
				$img = toba_recurso::imagen_proyecto('svn/normal.png', true, 16, 16, $nombre);
				break;		
			case 'conflicted':
				$nombre = 'En Conflicto';
				$img = toba_recurso::imagen_proyecto('svn/conflict.png', true, 16, 16, $nombre);
				break;	
			case 'deleted':
				$nombre = 'borrado';
				$img = toba_recurso::imagen_proyecto('svn/deleted.png', true, 16, 16, $nombre);
				break;
			case 'locked':
				$nombre = 'locked';
				$img = toba_recurso::imagen_proyecto('svn/locked.png', true, 16, 16, $nombre);
				break;
			case 'ignored':
				$svn_add = true;
				$svn_blame = false;
				$nombre = 'ignorado';
				$img = toba_recurso::imagen_proyecto('svn/ignored.png', true, 16, 16, $nombre);
				break;	
		}
		$this->info_archivo = "$path <span style='font-size: 9px; color:gray'>($nombre)</span>";
		$this->previsualizacion = $codigo;

		$existe_archivo = file_exists($path);
		$ver_comandos_svn = $svn->hay_cliente_svn();		
		if (! $svn->hay_cliente_svn()) {
			$ver_comandos_svn = false;
		}

		//-- Oculta boton eliminar
		if (! $existe_archivo) {
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_evento('abrir');
		}
		if (!$ver_comandos_svn || !$svn_blame) {
			$this->pantalla()->eliminar_evento('svn_blame');
		}			
		if (!$ver_comandos_svn || !$svn_diff) {
			$this->pantalla()->eliminar_evento('svn_diff');
			$this->pantalla()->eliminar_evento('svn_revert');
		}
		if (!$ver_comandos_svn || !$svn_add || !$existe_archivo) {
			$this->pantalla()->eliminar_evento('svn_add');
		}
		if (!$ver_comandos_svn || !$existe_archivo) {
			$this->pantalla()->eliminar_evento('trac_ver');
		}
	}
	
	function get_codigo_vista_previa()
	{
		$opciones = $this->get_opciones();
		$metodos = $this->get_metodos_a_generar();
		$archivo_php = new toba_archivo_php($this->s__path_archivo);
		
		//-- Se va a modificar algo?
		if ($this->s__es_esclavo && (! empty($metodos) || $archivo_php->esta_vacio())) {
			$clase_php = new toba_clase_php($archivo_php, $this->controlador()->get_metaclase());
			$codigo = $clase_php->get_codigo($metodos, $opciones['incluir_comentarios'], $opciones['incluir_separadores']);
			$codigo = "<?php\n".$codigo."\n?>";
			return $codigo;
		} else {
			//-- Muestra el original
			return file_get_contents($this->s__path_archivo);
		}
	}

	function get_path_archivo()
	{
		return $this->s__path_archivo;
	}
	
	function get_metodos_a_generar()
	{
		$metodos = array();
		if (isset($this->s__datos_metodos)) {
			foreach ($this->s__datos_metodos as $clave => $valor) {
				if ($valor) {
					$clave = explode('_', $clave);
					$metodos[] = end($clave);
				}
			}
		}		
		return $metodos;
	}
	
	function resetear_metodos()
	{
		$this->s__datos_metodos = array();		
	}

	//-------------------------------------------------------------------------------
	//-- Apertura de archivos por AJAX ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{
		$this->evt__abrir();
	}

	function evt__abrir()
	{
		$archivo_php = new toba_archivo_php($this->get_path_archivo());
		if( !$archivo_php->existe() ) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $archivo_php->nombre() . '\').');
		}
		$archivo_php->abrir();
	}

 
}

?>
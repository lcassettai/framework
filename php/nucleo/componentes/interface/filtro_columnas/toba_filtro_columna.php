<?php

/**
 * Contiene una condicion y un ef. Se trata de reutilizar al maximo la logica de los efs sin heredarlos, es por eso que muchas llamadas pasan directo
 * 
 **/
abstract class toba_filtro_columna
{
	protected $_datos;
	protected $_ef;
	protected $_padre;
	protected $_id_form_cond;
	protected $_estado = null;	
	protected $_condiciones = array();
	
	function __construct($datos, $padre) 
	{
		$this->_datos = $datos;
		$this->_padre = $padre;
		$this->_id_form_cond = "col_" . $this->_padre->get_id_form() . $this->_datos['nombre'];		
		$this->ini();
	}
	
	/**
	 * M�todo para construir el ef adecuado seg�n el tipo de columna
	 */
	abstract function ini();

	//-----------------------------------------------
	//--- COMANDOS ---------------------------------
	//-----------------------------------------------	
	
	function set_estado($estado)
	{
		$this->_estado = $estado;
		$this->_ef->set_estado($estado['valor']);
	}	
	
	function set_visible($visible)
	{
		$this->_datos['inicial'] = $visible;
	}	
	
	function set_expresion($campo)
	{
		$this->_datos['expresion'] = $campo;
	}
	
	function cargar_estado_post()
	{
		$this->_estado = array();	
		if (isset($_POST[$this->_id_form_cond])) {
			$condicion = $_POST[$this->_id_form_cond];
			if (! isset($this->_condiciones[$condicion])) {
				throw new toba_error("La condicion '$condicion' no es una condicion v�lida");
			}
			$this->_estado['condicion'] = $condicion;
		} else {
			throw new toba_error("No hay una condici�n valida");
		}

		$this->_ef->cargar_estado_post();			
		$this->_estado['valor'] = $this->_ef->get_estado();
		
	}	
	
	function agregar_condicion($id, toba_filtro_condicion $condicion)
	{
		$this->_condiciones[$id] = $condicion;
	}
	
	function borrar_condicion($id)
	{
		unset($this->_condiciones[$id]);
	}
	
	
	//-----------------------------------------------
	//--- CONSULTAS ---------------------------------
	//-----------------------------------------------
	
	
	
	function get_id_metadato()
	{
		return $this->_datos['objeto_ei_filtro_col'];
	}
	
	function get_id_form()
	{
		return $this->_padre->get_id_form();
	}
	
	function get_tab_index()
	{
		return $this->_padre->get_tab_index();
	}
	
	function es_obligatorio()
	{
		return $this->_ef->es_obligatorio();
	}
	
	
	function es_visible()
	{
		return $this->_datos['inicial'];
	}
	
	function es_compuesto()
	{
		return false;
	}
	
	function get_nombre()
	{
		return $this->_datos['nombre'];
	}
	
	function get_ef()
	{
		return $this->_ef;
	}
	
	function get_expresion()
	{
		return $this->_datos['expresion'];
	}

	function get_etiqueta()
	{
		return $this->_datos['etiqueta'];
	}


	function validar_estado()
	{
		return $this->_ef->validar_estado();
	}
	
	function resetear_estado()
	{
		$this->_ef->resetear_estado();
		$this->_estado = null;
	}
	
	function get_estado()
	{
		return $this->_estado;
	}
	
	function get_cant_condiciones()
	{
		return count($this->_condiciones);
	}
	
	/**
	 * Retorna una condici�n asociada a la columna, por defecto la que actualmente selecciono el usuario
	 * @return toba_filtro_condicion
	 */
	function condicion($nombre = null)
	{
		if (! isset($nombre)) {
			if (isset($this->_estado)) {
				return $this->_condiciones[$this->_estado['condicion']];
			} else {
				throw new toba_error_def("No hay una condicion actualmente seleccionada para la columna '".$this->get_nombre()."'");
			}
		} else {
			return $this->_condiciones[$nombre];
		}
	}
	
	function set_condicion(toba_filtro_condicion $condicion, $nombre=null)
	{
		if (! isset($nombre)) {
			if (isset($this->_estado)) {
				$this->_condiciones[$this->_estado['condicion']] = $condicion;
			} else {
				throw new toba_error_def("No hay una condicion actualmente seleccionada para la columna '".$this->get_nombre()."'");
			}
		} else {
			$this->_condiciones[$nombre] = $condicion;
		}		
	}
	
	
	function get_sql_where()
	{
		if (isset($this->_estado)) {
			$id = $this->_estado['condicion'];	
			return $this->_condiciones[$id]->get_sql($this->get_expresion(), $this->_estado['valor']);
		}
	}


	//-----------------------------------------------
	//--- SALIDA HTML  ------------------------------
	//-----------------------------------------------
	
	function get_html_condicion()
	{
		if (count($this->_condiciones) > 1) {
			//-- Si tiene mas de una condicion se muestran con un combo
			$onchange = "{$this->get_objeto_js()}.cambio_condicion(\"{$this->get_nombre()}\");";
			$html = "<select id='{$this->_id_form_cond}' name='{$this->_id_form_cond}' onchange='$onchange'>";
			foreach ($this->_condiciones as $id => $condicion) {
				$selected = '';
				if (isset($this->_estado) && $this->_estado['condicion'] == $id) {
					$selected = 'selected';	
				}
				$html .= "<option value='$id' $selected>".$condicion->get_etiqueta()."</option>\n";
			}
			$html .= '</select>';
			return $html;
		} else {
			$condicion = key($this->_condiciones);
			//-- Si tiene una unica, seria redundante mostrarle la unica opci�n, se pone un hidden
			return "<input type='hidden' id='{$this->_id_form_cond}' name='{$this->_id_form_cond}' value='$condicion'/>&nbsp;";
		}
	}	
	
	function get_html_valor()
	{
		echo $this->_ef->get_input();
	}

	function get_html_etiqueta()
	{
		$html = '';
		$marca ='';		
        if ($this->_ef->es_obligatorio()) {
    	        $estilo = 'ei-filtro-etiq-oblig';
				$marca = '(*)';
    	} else {
            $estilo = 'ei-filtro-etiq';
	    }
		$desc='';
		$desc = $this->_datos['descripcion'];
		if ($desc !=""){
			$desc = toba_parser_ayuda::parsear($desc);
			$desc = toba_recurso::imagen_toba("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->_ef->get_id_form();					
		$editor = '';		
		//$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->get_etiqueta();
		$html .= "<label for='$id_ef' class='$estilo'>$editor $desc $etiqueta $marca</label>\n";
		return $html;
	}
		
	
	//-----------------------------------------------
	//--- JAVASCRIPT   ------------------------------
	//-----------------------------------------------

	function get_objeto_js_ef($id)
	{
		return $this->_padre->get_objeto_js_ef($id);
	}
	
	function get_objeto_js()
	{
		return $this->_padre->get_objeto_js();
	}
		
	function get_consumo_javascript()
	{
		return $this->_ef->get_consumo_javascript();
	}
	
	function crear_objeto_js()
	{
		return $this->_ef->crear_objeto_js();
	}	
}

?>
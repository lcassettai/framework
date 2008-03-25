<?php 
class toba_rf_subcomponente_evento extends toba_rf_subcomponente
{
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'enter.png', false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);		
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$check_oculto = $this->oculto ? 'checked' : '';
		$html = '';
		$html .= "<LABEL for='$id_input'>Ocultar</LABEL>";
		$html .= "<input type='checkbox' $check_oculto value='1' id='$id_input' name='$id_input' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{

		if (isset($_POST[$id.'_oculto'])) {
			$this->oculto = $_POST[$id.'_oculto'];
		} else {
			$this->oculto = false;
		}		
	}	
}
?>
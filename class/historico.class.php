<?php

class historico{
	
	protected $id;
	protected $tabela;
	protected $id_tabela;
	protected $id_usuario;
	protected $acao;
	protected $data;

	function __construct($id, $tabela = null, $id_tabela = null, $acao = null){
		if($id){

		}else{
			$this->tabela 		= $tabela;
			$this->id_tabela 	= $id_tabela;
			if(isset($_COOKIE['usuario'])){
				$this->id_usuario 	= registro($_COOKIE['usuario'], 'usuario', 'id', 'email');
			}else{
				$this->id_usuario 	= 1;
			}
			$this->acao 		= $acao;
			$this->data 		= date('Y-m-d H:i:s');
		}
	}

	function update(){
		$conn = TConnection::open(ALCUNHA);

		$sql = new TSqlInsert;
		$sql->setEntity('historico');
		$sql->setRowData('tabela_afetada', $this->tabela);
		$sql->setRowData('chave_principal', $this->id_tabela);
		$sql->setRowData('id_usuario', $this->id_usuario);
		$sql->setRowData('acao', $this->acao);
		$sql->setRowData('data', $this->data);

		$result = $conn->query($sql->getInstruction());
	}

}


?>
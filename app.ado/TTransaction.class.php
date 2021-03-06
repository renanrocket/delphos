<?php

/**
 * classe TTransaction
 * esta classe provê os métodos necessários para manipular transações
 */
final class TTransaction {

	private static $conn;	//conexão ativa

	/*
	 * método __construct()
	 * Está declarado como private para impedir que se crie instâncias de TTransaction
	 */
	private function __construct() {
	}

	/*
	 * método open()
	 * Abre uma transação e uma conexão ao BD
	 * @param $database = nome do banco de dados
	 */
	public static function open($database) {
		//abre uma conexão e armazena na propriedade estática $conn
		if(empty(self::$conn)){
			self::$conn = TConnection::open($database);
			//inicia a transação
			self::$conn->beginTransaction();
		}
	}
	
	/*
	 * método get()
	 * retorna a coneção ativa da transação
	 */
	public static function get(){
		//retorna a conexão ativa
		return self::$conn;
	}
	
	/*
	 * método rollback()
	 * desfaz todas operações realizadas na transação
	 */
	public static function rollback(){
		if(self::$conn){
			//desfaz as operações realizadas durante a transação
			self::$conn->rollback();
			self::$conn = null;
		}
	}
	
	/*
	 * método close()
	 * Aplica todas operações realizadas e fecha a transação
	 */
	public static function close(){
		if(self::$conn){
			//aplica as operações realizadas
			//durante a transação
			self::$conn->commit();
			self::$conn = null;
		}
	}

}
?>
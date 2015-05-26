// JavaScript Document

//<!--
function filtro(){
	with(document.formCadastraEmpresa){
		var alerta = "";
		var confirma = "";
		var valida = true;
		
		function existir(variavel, texto){
			if (variavel.value=="" || (variavel.value.match(/^\s+$/)) || variavel.value==0){
				alerta += texto;
				variavel.className = "avisoInput";
				valida = false;
			}else{
				variavel.className = "";
			}
		}
		
		//existir(razao_social, "Preencha o campo Razao Social.\n");
		existir(nome, "Preencha o campo Nome fantasia da empresa.\n");
		existir(endereco, "Informe o endereco da empresa.\n");
		existir(estados, "Selecione o Estado onde esta localizado a empresa.\n");
		existir(cidade, "Selecione a Cidade onde esta localizado a empresa.\n");
		existir(fone1, "Informe pelo menos um telefone de contato.\n");
		
		if (valida==true){
			return true;
		}else{
			alert(alerta);
			return false;
		}
	}
}

//-->
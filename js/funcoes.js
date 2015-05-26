function resolucao() {

	//capturando a resolução da tela e setando a porcentagem de tamanho
	sh = screen.height * 16 / 100
	swMenor = screen.width * 15 / 100
	swMaior = screen.width * 85 / 100

	//setando o tamanho do menuMain e main e a posicao da primeira tabela
	document.getElementById('tableMain').style.top = sh + 'px'
	document.getElementById('menuMain').style.width = swMenor + 'px'
	document.getElementById('main').style.width = swMaior + 'px'

	//encontrando o tamanho vertical da tabela
	heightTable = document.getElementById('menuMain').offsetHeight + 100
	
}



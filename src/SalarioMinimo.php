<?php 

namespace EuFelipeMateus;

use GuzzleHttp;
use DOMDocument;

class SalarioMinimo
{
	private static $url = 'http://www.guiatrabalhista.com.br/guia/salario_minimo.htm';
	
    private static function getList()
    {
		//iniciando variaveis
		$client = new GuzzleHttp\Client();
		$doc = new DOMDocument();
		$index=0;
		$ResultadoFinal;


		$response = $client->get(self::$url);//pegando site

		preg_match('/<table.*?>(.*?)<\/table>/si', $response->getBody(), $matches); //pegar trecho de codigo onde esta a tabela
	
		$doc->loadHTML($matches[0]);//Carregando tabela no domdocument.

		$tbody = $doc->getElementsByTagName('tbody')->item(0);
	
		$tr= $tbody->getElementsByTagName('tr'); //ir para primeira linha
		
		for($i=1; $i< $tr->length; $i++ ){

			$tds = $tr->item($i)->getElementsByTagName('td'); 		
			
			//colocar valores no array final
			$ResultadoFinal[$index]["vigencia"] = trim($tds->item(0)->textContent);
			$ResultadoFinal[$index]["valor_mensal"] = self::parserMoney($tds->item(1)->textContent);
			$ResultadoFinal[$index]["valor_diario"] = self::parserMoney($tds->item(2)->textContent);
			$ResultadoFinal[$index]["valor_hora"] = self::parserMoney($tds->item(3)->textContent);
			$ResultadoFinal[$index]["norma_legal"] = trim($tds->item(4)->textContent);
			$ResultadoFinal[$index]["dou"] = trim($tds->item(5)->textContent);
			
			$index++; //Somar valor do indice
		}
		
		return $ResultadoFinal;
	}
	
	private static function parserMoney($valor){
		$valor = trim($valor);
		$valor = preg_replace('/\s+/', '', $valor);
		return  str_replace('R$', '', $valor);
	}
	
	public static function getSalarioAtual(){
		return self::getList()[0];
	}
	
	public static function getArray(){
		return self::getList();
	}
	
	public static function getJson(){
		return json_encode(SalarioMinimo::getArray()); //retornando resultado final
	}
}
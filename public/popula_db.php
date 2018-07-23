<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->map(['GET'],'/popula', function (Request $request, Response $response, array $args) {
	
	$data_time = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	
	$pascoa = dataPascoa(2019);
	$corpus_christi = dataCorpusChristi(2018);
	$carnaval = dataCarnaval(2019);
	$sexta_santa = dataSextaSanta(2018);
	echo " Pascao: $pascoa <br>";
	echo " Carnaval: $carnaval <br>";
	echo " corpus_christi: $corpus_christi <br>";
	echo " Sexta Santa: $sexta_santa <br>";
	
	$i = 0;
	
	$feriados = array("01-01", $carnaval, $sexta_santa, $pascoa, $corpus_christi,
	"04-21", "05-01", "06-12" ,"07-09", "07-16", "09-07", "10-12", "11-02", 
	"11-15", "12-24", "12-25", "12-31");

	while ($i < 360){
		$data_time->modify('+1 day');
		$i++;
		$data = $data_time->format('Y-m-d');
		$data_mes_dia = $data_time->format('m-d');
		if (in_array($data_mes_dia,$feriados))
			echo "Feriado: $data <br>";
		//echo "$data <br>";
	}
		
	
	
	
	

});

function dataPascoa($ano=false, $form="Y-m-d") {
		$ano=$ano?$ano:date("Y");
		if ($ano<1583) { 
			$A = ($ano % 4);
			$B = ($ano % 7);
			$C = ($ano % 19);
			$D = ((19 * $C + 15) % 30);
			$E = ((2 * $A + 4 * $B - $D + 34) % 7);
			$F = (int)(($D + $E + 114) / 31);
			$G = (($D + $E + 114) % 31) + 1;
			return date($form, mktime(0,0,0,$F,$G,$ano));
		}
		else {
			$A = ($ano % 19);
			$B = (int)($ano / 100);
			$C = ($ano % 100);
			$D = (int)($B / 4);
			$E = ($B % 4);
			$F = (int)(($B + 8) / 25);
			$G = (int)(($B - $F + 1) / 3);
			$H = ((19 * $A + $B - $D - $G + 15) % 30);
			$I = (int)($C / 4);
			$K = ($C % 4);
			$L = ((32 + 2 * $E + 2 * $I - $H - $K) % 7);
			$M = (int)(($A + 11 * $H + 22 * $L) / 451);
			$P = (int)(($H + $L - 7 * $M + 114) / 31);
			$Q = (($H + $L - 7 * $M + 114) % 31) + 1;
			return date($form, mktime(0,0,0,$P,$Q,$ano));
		}
}

function dataSextaSanta($ano=false, $form="Y-m-d") {
	$ano=$ano?$ano:date("Y");
	$a=explode("-", dataPascoa($ano));
	
	return date($form, mktime(0,0,0,$a[1],$a[2]-2,$a[0]));
} 

function dataCorpusChristi($ano=false, $form="Y-m-d") {
	$ano=$ano?$ano:date("Y");
	$a=explode("-", dataPascoa($ano));
	return date($form, mktime(0,0,0,$a[1],$a[2]+60,$a[0]));
}

function dataCarnaval($ano=false, $form="Y-m-d") {
	$ano=$ano?$ano:date("Y");
	$a=explode("-", dataPascoa($ano));
	return date($form, mktime(0,0,0,$a[1],$a[2]-47,$a[0]));
}

?>
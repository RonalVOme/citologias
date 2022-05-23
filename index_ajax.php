<?php
include("config/conexion.php");

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=citologia.xls");

if($_GET["Accion"]=="generarArchivo"){
	//Se obtienen las fecha del reporte
	$Fecha_Inicial = $_GET['Fecha_Inicial'];
	$Fecha_Final = $_GET['Fecha_Final'];

	$sql="SELECT c.ConsAdmi, c.NumeCito, ca.NombAdmi, p.NombUsua, p.NombUsu1, p.Ape1Usua, p.Ape2Usua, p.TipoDocu, p.NumeUsua, cm.NombMuni, p.DireResi, p.TeleCelu, p.FechNaci, a.ValoEdad,
							 c.FeUlMens, c.Gestacio, c.Partos, c.Cesareas, c.Abortos,c.CondUsua,c.EmbaNoSa ,cu.NombCond, cp.NombMePl, dp.TiemUso, c.OtMePlan, c.TiOtMePl, c.FechToan, c.ResuAnte, c.ObseAnte,
							 da.CodiAsCu, cc.NombAscu,  c.TomoCito, c.Cargo, c.FechToma,c.ObseToma
				FROM Admision AS a
				INNER JOIN Paciente AS p ON(a.TipoDocu = p.TipoDocu AND a.NumeUsua = p.NumeUsua)
				INNER JOIN Citologi AS c ON(c.CodiInst = a.CodiInst AND c.ConsAdmi = a.ConsAdmi)
				INNER JOIN CodiAdmi AS ca ON (a.CodiAdmi = ca.CodiAdmi)
				INNER JOIN CodiMuni AS cm ON (p.ResiMuni = cm.CodiMuni)
				INNER JOIN CondUsua AS cu ON (a.CondUsua = cu.CodiCond)
				LEFT JOIN CodiMePl AS cp ON (c.TiOtMePl = cp.CodiMePl)
				LEFT JOIN DetaMePl AS dp ON (cp.CodiMePl = dp.CodiMePl)
				LEFT JOIN DetaAsCu AS da ON (c.ConsAdmi = da.ConsAdmi)
				LEFT JOIN CodiAsCu AS cc ON (da.CodiAsCu = cc.CodiAsCu)
				WHERE c.FechToma >= '$Fecha_Inicial' AND c.FechToma <= '$Fecha_Final'
				GROUP BY c.ConsAdmi;";
	$res=queryli($sql,2);

	$Retorno = array();

	if($Filas>0){
		$i = 0;

		$Cadena = '<table border=1 cellpadding=0 cellspacing=0><tr>';
		$Cadena .= "<td nowrap>InternoCitologia</td><td nowrap>NombreEntidad</td><td nowrap>1Nombre</td><td nowrap>2Nombre</td><td nowrap>1Apellido</td><td nowrap>2Apellido</td><td nowrap>TipoIdentificacion</td><td nowrap>NumeroId</td><td nowrap>NombreMunicipio</td><td nowrap>Direccion</td><td nowrap>Telefono</td><td nowrap>FechaNacimiento</td><td nowrap>EdadAnosTT</td><td nowrap>FUM</td><td nowrap>ObservacionesFUM</td><td nowrap>GPAC</td><td nowrap>Embarazo</td><td nowrap>Lactancia</td><td nowrap>DescripcionMetodoPlan</td><td nowrap>TiempoPlanificacion</td><td nowrap>ObsPlanificacion</td><td nowrap>ResultadoUltimaCitologia</td><td nowrap>TiempoCitologia</td><td nowrap>ObsCitologia</td><td nowrap>TiempoVPH</td><td nowrap>TiempoTHR</td><td nowrap>TiempoCauterizacion</td><td nowrap>TiempoRadioterapia</td><td nowrap>TiempoConizacion</td><td nowrap>TiempoHisterectomia</td><td nowrap>ObsProcedCervix</td><td nowrap>opcAspectoCuello</td><td nowrap>ObsGenerales</td><td nowrap>Nombre</td><td nowrap>FechaToma";
		$Cadena .= '</tr>';

		foreach ($res as $key => $value) {
			//Fecha de Nacimiento
			$Fecha_Nacimiento = explode('-', $value['FechNaci']);
			$Fecha_Nacimiento = $Fecha_Nacimiento[2].'/'.$Fecha_Nacimiento[1].'/'.$Fecha_Nacimiento[0];

			//Fecha de ultima menstruacion
			$FechaUltimaMenstruacion = explode('-', $value['FeUlMens']);
			$FechaUltimaMenstruacion = $FechaUltimaMenstruacion[2].'/'.$FechaUltimaMenstruacion[1].'/'.$FechaUltimaMenstruacion[0];

			//Embarazo
			$Embarazo = $value['CondUsua'];

			if($Embarazo<=3)
				$Embarazo = 1;
			else
				$Embarazo = 2;

			if($value['EmbaNoSa']==1)
				$Embarazo = 3;

			//Se consulta el metodo de planificacion
			$sql="SELECT c.NombMePl,d.TiemUso
						FROM DetaMePl d
						INNER JOIN Admision a ON(a.CodiInst=d.CodiInst AND a.ConsAdmi=d.ConsAdmi)
						INNER JOIN CodiMePl c ON(c.CodiMePl=d.CodiMePl)
						WHERE a.ConsAdmi = '$value[ConsAdmi]'
						ORDER BY d.CodiMePl DESC";
			$res=queryli($sql,1);
			$MetoPlan = 'NO USA';
			$TiemUso = '';
			if($Filas>0){
				$MetoPlan = $res['NombMePl'];
				$TiemUso = $res['TiemUso'];

				//Se extrae la informaicón del tiempo del uso
				$datosTiempo = explode(' ', $TiemUso);

				$Meses = $datosTiempo[0];
				$AnoMes = $datosTiempo[1];

				if($AnoMes=="A")
					$TiemUso = $Meses*12;
				else if($AnoMes=="M")
					$TiemUso = $Meses;
			}

			if($value['OtMePlan']!=""){
				$MetoPlan = $value['OtMePlan'];
				$TiemUso = $value['TiOtMePl'];

				//Se extrae la informaicón del tiempo del uso
				$datosTiempo = explode(' ', $TiemUso);

				$Meses = $datosTiempo[0];
				$AnoMes = $datosTiempo[1];

				if($AnoMes=="A")
					$TiemUso = $Meses*12;
				else if($AnoMes=="M")
					$TiemUso = $Meses;

			}

			$ResuAnte = '';
			//validacion para resultado anterior de citologia
			$textoResultadoAnterior = $value['ResuAnte'];
			$buscar   = 'NORMAL';
			$buscar2   = 'CUELLO SANO';
			$buscar3   = 'NEGATIVA';

			if($textoResultadoAnterior==$buscar || $textoResultadoAnterior==$buscar2 || $textoResultadoAnterior==$buscar3)
				$ResuAnte = "1";

			$buscar4   = 'POSITIVA';
			$buscar5   = 'ANORMAL';

			if($textoResultadoAnterior==$buscar4 || $textoResultadoAnterior==$buscar5)
				$ResuAnte = "2";

			$buscar6   = 'NO SABE';
			$buscar7   = 'NO CONOCE';
			$buscar8   = 'NO RECUERDA';

			if($textoResultadoAnterior==$buscar6 || $textoResultadoAnterior==$buscar7 || $textoResultadoAnterior==$buscar8)
				$ResuAnte = "3";

			$buscar9   = '';
			$buscar10   = 'NO REALIZA';
			$buscar11   = 'NO REALIZADO';

			if($textoResultadoAnterior==$buscar9 || $textoResultadoAnterior==$buscar10 || $textoResultadoAnterior==$buscar11)
				$ResuAnte = "4";

			if($ResuAnte=="")
				$ResuAnte = "4";
			//Se consulta el aspecto del cuello uterino
			$sql="SELECT d.CodiAsCu,c.NombAscu
						FROM DetaAsCu d
						INNER JOIN CodiAsCu c ON(c.CodiAsCu=d.CodiAsCu)
						WHERE ConsAdmi='$value[ConsAdmi]'
						ORDER BY d.FechDigi DESC";
      $res2=queryli($sql,2);

      if($Filas>0){
      	$Sano = $Ausente = $Atrofico = $Erosionado = $Lesion = $Ulcerado = $Congestivo = $Sangrado = 0;
      	foreach ($res2 as $key => $value2) {

      		if($value['CodiAsCu']==1)
      			$Sano = 1;

      		if($value['CodiAsCu']==2)
      			$Ausente = 1;

      		if($value['CodiAsCu']==3)
      			$Atrofico = 1;

      		if($value['CodiAsCu']==0)
      			$Erosionado = 1;

      		if($value['CodiAsCu']==7)
      			$Lesion = 1;

      		if($value['CodiAsCu']==6)
      			$Ulcerado = 1;

      		if($value['CodiAsCu']==5)
      			$Congestivo = 1;
      	}

      	$Aspecto = $Sano.$Ausente.$Atrofico.$Erosionado.$Lesion.$Ulcerado.$Congestivo;

      }
      else
      	$Aspecto = '00000000';

      //Conversion de binario a decimal
      $Aspecto = bindec($Aspecto);

      $ObseAnte = substr($value['ObseAnte'], 0,255);
      $ObseAnte = strtoupper($ObseAnte);

      $ObseToma = substr($value['ObseToma'], 0,255);
      $value['DireResi'] = substr($value['DireResi'], 0,30);

			$Cadena.= '<tr><td nowrap>'.$value['NumeCito'].'</td><td nowrap>'.$value['NombAdmi'].'</td><td nowrap>'.$value['NombUsua'].'</td><td nowrap>'.$value['NombUsu1'].'</td><td nowrap>'.$value['Ape1Usua'].'</td><td nowrap>'.$value['Ape2Usua'].'</td><td nowrap>'.$value['TipoDocu'].'</td><td nowrap>'.$value['NumeUsua'].'</td><td nowrap>'.$value['NombMuni'].'</td><td nowrap>'.$value['DireResi'].'</td><td nowrap>'.$value['TeleCelu'].'</td><td nowrap>'.$Fecha_Nacimiento.'</td><td nowrap>'.$value['ValoEdad'].'</td><td nowrap>'.$FechaUltimaMenstruacion.'</td><td nowrap></td><td nowrap>'.$value['Gestacio'].','.$value['Partos'].','.$value['Abortos'].','.$value['Cesareas'].',,</td><td nowrap>'.$Embarazo.'</td><td nowrap>FALSO</td><td nowrap>'.strtoupper($MetoPlan).'</td><td nowrap>'.$TiemUso.'</td><td nowrap></td><td nowrap>'.$ResuAnte.'</td><td nowrap></td><td nowrap>'.$ObseAnte.'</td><td nowrap></td><td nowrap></td><td nowrap></td><td nowrap></td><td nowrap></td><td nowrap></td><td nowrap></td><td nowrap>'.$Aspecto.'</td><td nowrap>'.strtoupper($ObseToma).'</td><td nowrap>'.$value['TomoCito'].'-'.$value['Cargo'].'</td><td nowrap>'.$value['FechToma'].'</tr>';
			$i++;
		}

		$Cadena.='</table>';

		echo $Cadena;
	}
	else
		echo 2;

}







?>

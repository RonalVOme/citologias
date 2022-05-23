<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">

    <style type="text/css">
			.loader {
			    position: fixed;
			    left: 0px;
			    top: 0px;
			    width: 100%;
			    height: 100%;
			    z-index: 9999;
			    background: url('loading.gif') 50% 50% no-repeat rgb(249,249,249);
			    opacity: .8;
			}
    </style>
</head>

<body>
<div class="loader" id="loader" style="display: none" >
</div>

    <div class="container" id="registration-form">
        <div class="image"></div>
        <div class="frm">
            <h1>Toma de Citologia</h1>
            <form>
                <div class="form-group">
                    <label for="Fecha_Inicial">Fecha Inicial:</label>
                    <input type="date" class="form-control" id="Fecha_Inicial" placeholder="Fecha Inicial">
                </div>
                <div class="form-group">
                    <label for="Fecha_Final">Fecha Final:</label>
                    <input type="date" class="form-control" id="Fecha_Final" placeholder="Fecha Final">
                </div>

                <div class="form-group">
                    <button type="button" class="btn  btn-lg" style="background-color: pink !important" id="GenerarCSV">Generar</button>
                    <a id="Descargar" href="citologia.csv" class="btn btn-lg active" style="background-color: pink !important; display: none" role="button">Descargar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


<script type="text/javascript">

$(document).ready(function(){

	$("#GenerarCSV").click(function(event){

		if($("#Fecha_Inicial").val()==""){
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'Debe seleccionar la fecha inicial!',
			})
			return false;
		}

		if($("#Fecha_Inicial").val()!="" && $("#Fecha_Final").val()==""){
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'Debe seleccionar la fecha final!',
			})
			return false;
		}

		if($("#Fecha_Inicial").val() > $("#Fecha_Final").val()){
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'La fecha inicial no puede ser superior a la fecha final!',
			})

			return false;
		}

		$("#Descargar").css("display","none");
		$("#loader").css("display","block");

		setTimeout(function(){
			Swal.fire({
			  icon: 'success',
			  title: 'Terminado',
			  text: 'Se ha generado el archivo plano correctamente',
			})
			$("#loader").css("display","none");

		}, 2000);
		window.open("index_ajax.php?Accion=generarArchivo&Fecha_Inicial="+$("#Fecha_Inicial").val()+"&Fecha_Final="+$("#Fecha_Final").val());

	});


});


</script>

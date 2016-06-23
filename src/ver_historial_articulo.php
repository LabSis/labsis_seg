<?php
require_once '../config.php';

$sesion = Session::get_instance();

$metodo = filter_input(INPUT_SERVER, "REQUEST_METHOD");
if (strcasecmp($metodo, "POST") === 0) {

    //header("Location: tecnica.php?id=$id_tecnica");
} else if (strcasecmp($metodo, "GET") === 0) {
    $id_articulo = filter_input(INPUT_GET, "id_articulo");
    if(isset($id_articulo) && is_numeric($id_articulo)){
        $historial_articulos = ApiBd::obtener_historial_articulos($id_articulo);
        array_unshift($historial_articulos, array("fecha_hora" => "Actual", "id" => "-1"));
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Historial de artículo</title>
        <link href="<?php echo $WEB_PATH ?>/css/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <style>
            .historico{
                position: float;
                float: left;
                margin-right: 40px;
                border-right: 1px solid black;
            }
            .historico li{
                cursor: pointer;
                margin: 5px;
            }
            .editor{
                position: float;
                float: left;
                width: 70%;
            }
            .seleccionado{
                font-weight: bold;
            }
        </style>
        <script type="text/javascript" src="<?php echo $WEB_PATH ?>/js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo $WEB_PATH ?>/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="<?php echo $WEB_PATH ?>/css/bootstrap/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function(){
                CKEDITOR.replace("txtEdicion");
                $("#spanTituloEdicion").text("Actual");
                $(".historico li").click(function(){
                    var versionSeleccionada = $(this).text();
                    var idVersion = $(this).data("id-version");
                    var idArticulo = $("#hidIdArticulo").val();
                    $.ajax({
                        "type": "post",
                        "url": "consultar_version_articulo.php",
                        "data": {
                            "id_version": idVersion,
                            "id_articulo": idArticulo
                        }
                    }).done(function(r){
                        r = JSON.parse(r);
                        if(r.status === "ok"){
                            CKEDITOR.instances['txtEdicion'].setData(r.version);
                        }
                    });

                    $("#spanTituloEdicion").text(versionSeleccionada);
                    $(".seleccionado").removeClass("seleccionado");
                    $(this).addClass("seleccionado");
                });
            });
        </script>
    </head>
    <body>
        <main class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2>Historial de artículos</h2>
                    <hr/>
                </div>
            </div>
            <input type="hidden" id="hidIdArticulo" value="<?php echo $id_articulo ?>" />
            <ul class="historico">
                <?php foreach($historial_articulos as $cambio): ?>
                    <?php if($cambio['fecha_hora'] === "Actual") :?>
                        <li class="seleccionado" data-id-version="<?php echo $cambio["id"] ?>" ><?php echo $cambio['fecha_hora'] ?></li>
                    <?php else: ?>
                        <li data-id-version="<?php echo $cambio["id"] ?>" ><?php echo $cambio['fecha_hora'] ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="editor row" >
                <form role="form" action="guardar_articulo.php?id=<?php echo $id_tecnica ?>" method="post">
                    <h4>Edición de la versión de <span id="spanTituloEdicion"></span></h4>
                    <div class="form-group">
                        <textarea class="form-control" rows="20" id="txtEdicion" ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Aceptar</button>
                </form>
            </div>
        </main>
    </body>
</html>


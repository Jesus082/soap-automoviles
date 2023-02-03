<!DOCTYPE html>
<html>
<head>
    <style>
        figure {
            border: 1px #cccccc solid;
            padding: 4px;
            margin: auto;
        }

        figcaption {
            background-color: navy;
            color: white;
            font-weight: bolder;
            font-style: italic;
            padding: 2px;
            text-align: center;
        }
    </style>
</head>
<body>
<h1>Modelos disponibles marca: <?=$_GET['marca']?></h1>

    <?php
    require "cliente-automoviles.php";
    $marca = $_GET['marca'];
    $modelos = $client->ObtenerModelosPorMarca($marca);
    foreach($modelos as $modelo){ 
    ?>
    <figure>
        <img src="images/<?=strtolower($marca)?>.png" alt="logo <?=strtolower($marca)?>" width="200" />
        <figcaption><?=$modelo['modelo']?></figcaption>
        </figure>
    <?php
    }
    ?>

</body>
</html>
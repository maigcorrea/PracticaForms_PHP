<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://bootswatch.com/5/morph/bootstrap.min.css">
    <style>
        table{
            border:1px solid black;
            border-collapse:collapse;
        }
        td{
            border:solid 1px black;
        }
        th{
            border:solid 1px black;
        }
    </style>
</head>
<body>
  
    <?php

    $form=$_POST;
    if(isset($_POST["textos"])){
        $imagen=$_FILES["img"];
        $radio=$_POST["rad"];
        $ubi="procesamiento.php";//Almacenamos el nombre del archivo php para luego reenviar los errores

        //Si no ha introducido la foto, que salga un error

        //Si no ha seleccionado un radio, que salga un error
           
        //Si no ha seleccionado un radio ni una foto, que salga un error

        //    if(!isset($imagen)){
        //         header("Location: $ubi?err=1");
        //    }elseif (!isset($radio)) {
        //         header("Location: $ubi?err=2");
        //    }elseif (!isset($radio) && !isset($imagen)) {
        //         header("Location: $ubi?err=3");
        //     };
        
            
        //TABLA
        echo "<table>";
        echo "<tr><th>Cadena Evaluada</th><th>Categorías</th></tr>";
            foreach ($form as $key => $input) {
                $categorias=[]; //Array donde se van a ir almacenando todas las categorias de cada cadena
                
                //Cadena vacia
                if($input==""){
                   $categorias[]="Cadena vacía";
                }
                
                //Cadena única. TAMBIÉN ME TIENE QUE PILLAR EL NÚMERO DE TELÉFONO COMO CADENA ÚNBICA
                if(preg_match("'\s?+[a-zA-Z]+\s*'",$input)){
                    $categorias[]="Cadena con una única palabra";
                }
                
                
                //Cadena con dos palabras. TAMBIÉN TIENE QUE PILLA EL NUMERO DE TELEFONO, DNI , ETC
                if(preg_match("'\s*[a-zA-Z]+\s+[a-zA-Z0-9]+\s*'",$input)){
                    $categorias[]="Cadena con dos palabras";
                }


                // //Cadena con una enumeración
                if(count(explode(",",$input))>=3){
                    $categorias[]="Cadena enumerada";
                }


                //Cadena con un número decimal
                if(preg_match("'(?<!\d)\d+\.\d+(?!\d)'",$input)){//hola23,14
                    $categorias[]="Cadena con número decimal";
                }

                //PUEDE SER DECIMAL E IMPAR AL MISMO TIEMPO Y TELÉFONO E IMPAR AL MISMO TIEMPO. La parte entera DEL NÚMERO DECIMAL ES LA IMPAR

                //Cadena con un único número impar
                if(preg_match("'^[^0-9]*[0-9]*[13579](\.[0-9]+)?[^0-9]*$'",$input)){
                    //^[^0-9]*[0-9]*[13579](\.[0-9]+)?[^0-9]*$
                    $categorias[]="Cadena con un número impar";
                }

                //Número de teléfono
                // ^\+(\d{2})?[6789]\d{8}$ o ^\+([0-9]{2})?[6789][0-9]{8}$
                if(preg_match("'^\+([0-9]{2})?\s?[6789][0-9]{8}$'",$input)){
                    $categorias[]="Número de teléfono";
                }


                //DNI
                if(preg_match("'^[0-9]{8}[A-Z]$'",$input)){
                    $categorias[]="DNI";
                }

                //Contraseña
                //^(?=.*[A-Z])(?=(?:[^0-9]*[0-9]){2})(?=(?:[^!@#$%^&*()_+]*[!@#$%^&*()_+]){3})[A-Za-z0-9!@#$%^&*()_+]{8,20}$
                if(preg_match("'^(?=.*[A-Z])(?=(?:[^0-9]*[0-9]){2})(?=(?:[^!@#$%^&*()_+]*[!@#$%^&*()_+]){3})[A-Za-z0-9!@#$%^&*()_+]{8,20}$'",$input)){
                    $categorias[]="Contraseña";
                    // ^(?=.[0-9]{2})(?=.[A-Z])(?=.*[\W_]{3}).{8,20}$
                    //^(?=.*[A-Z])(?=.*[0-9].*[0-9])(?=.*[\W].*[\W_].*[\W_]){8,20}$
                    //^(?=.*[A-Z])(?=.*[0-9].*[0-9])(?=.*[^\d^\w^\W].*[^\d^\w^\W].*[^\d^\w^\W])(){8,20}$   BUENA
                }

                if ($key !== "rad" && $key !== "textos") { // Condición para omitir "rad" y "textos"
                    echo "<tr><td>$input</td><td>".implode(",",$categorias)."</td></tr>";//Se hace implode para unir las categorias y separarlas mediante , .Así salen todas las categorias en un único td
                }

                //RESULTADOS DE INPUT RADIO
                $seleccionRadio=$_POST["rad"];//Almaceno el radio seleccionado por el usuario (Almaceno el value, que es el name de los input de tipo texto)
                $valorInput=$_POST[$seleccionRadio];//Almaceno el valor del input de tipo texto que el usuario ha seleccionado en el radio. Es el nuevo nombre que tendrá la imagen

            }
        echo "</table>";

        //GUARDAR LA IMÁGEN Y CAMBIAR EL NOMBRE
        $ruta="./img/";

        if(!file_exists($ruta)){
            mkdir($ruta);
        }

        
        
        $nomOrig=$_FILES["img"]["name"];//El nombre original de la imagen
        


        //Si el nuevo nombre no tiene la extensión, hay que añadirsela para que el archivo se pueda ver correctamente
        
        $extension;
        if(!preg_match("'^[a-zA-Z0-9]+\.[a-z]+$'",$valorInput)){//Si el nuevo nombre no va seguido de . y la extensión, se le añade la extensión del nombre original
            //Se concatena el nombre nuevo que se le va a poner a la imagen con la extensión del nombre original

            $pos = strrpos($nomOrig, '.'); // Encuentra la posición del último punto dentro del nombre original
            $extension=substr($nomOrig,$pos);//substr devuelve una parte del string a partir de una posición, en este caso devuelve una cadena a partir de la posición del . en el nombre original
            $valorInput=$valorInput.$extension;//Se concatena el nuevo nombre con la extensión
        }
        
        $origen=$_FILES["img"]["tmp_name"];
        $destino=$ruta.$valorInput; //Se concatena la ruta donde queremos guardar la imagen con el nuevo nombre


        move_uploaded_file($origen,$destino);

        echo '<img src="'.$ruta.$valorInput.'"<br>';
    }else{

        /* El value de los input de tipo radio es el name de los input de tipo texto, para luego poder almacenar la cadena introducida por el usuario */
        echo '
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="text" name="t1" placeholder="Introduce el texto 1"><br>
                <input type="text" name="t2" placeholder="Introduce el texto 2"><br>
                <input type="text" name="t3" placeholder="Introduce el texto 3"><br>
                <input type="text" name="t4" placeholder="Introduce el texto 4"><br>
                <input type="text" name="t5" placeholder="Introduce el texto 5"><br>
                <input type="text" name="t6" placeholder="Introduce el texto 6"><br>
                <input type="text" name="t7" placeholder="Introduce el texto 7"><br>
                <label for="img">Introduce una imagen:</label><br>
                <input type="file" name="img" accept="image/*" required><br>
                <label for="">Qué cadena quieres seleccionar?:</label><br>
                Cadena 1:<input type="radio" name="rad" value="t1" required><br>
                Cadena 2:<input type="radio" name="rad" value="t2" required><br>
                Cadena 3:<input type="radio" name="rad" value="t3" required><br>
                Cadena 4:<input type="radio" name="rad" value="t4" required><br>
                Cadena 5:<input type="radio" name="rad" value="t5" required><br>
                Cadena 6:<input type="radio" name="rad" value="t6" required><br>
                Cadena 7:<input type="radio" name="rad" value="t7" required><br>
                <input type="submit" value="Enviar" name="textos">
            </form>
        ';

        // if(isset($_GET["err"])){
        //     if($_GET["err"] == 1) echo "<p style=\"color:red\">Introduce la imagen</p>";
        //     if($_GET["err"] == 2) echo "<p style=\"color:red\">Selecciona una cadena</p>";
        //     if($_GET["err"] == 3) echo "<p style=\"color:red\">Introduce una imagen y selecciona una cadena</p>";
        // }
    }

    ?>
</body>
</html>
<DOCTYPE html>
<html>
    <title>
    </title>
    <body>

        <?php
    $php_var = 1;
    ?>

    <script>
    var js_var = <?=$php_var?>;
    alert(js_var); // 1출력
    </script>
    </body>
</html>

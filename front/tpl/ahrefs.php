<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>


<body>

<form action="/<?= SITE_FOLDER_PRE ?>/ahrefs/" method="post">

    <select name="<?= SITE_FOLDER_PRE ?>account_id" id="">
        <? foreach ($account_list as $item) { ?>
            <option value="<?= $item['id'] ?>"><?= $item['id'] ?></option>
        <? } ?>
    </select>

    <br>
    <input type="submit" value="submit">
</form>

</body>


</html>
<?php
header("Access-Control-Allow-Origin: *");
?>

<div class="body" id="about">
    <h1>Team Bromölla ka!</h1>
    <div id="peoples">
        <?php
        foreach (json_decode($_POST["persons"]) as $person) {
            ?>
            <Person name="<?=$person->name?>" focusArea={<?=json_encode(array_values($person->focusAreas))?>}></Person>
            <?php
        }
        ?>
    </div>
</div>


<h2>Your Custom Settings</h2>
<br>
<p><u>hint:</u> Use <code>Ctrl</code>/<code>CMD</code> + <code>F</code> to search for your config key or value.</p>
<p><u>pro tips:</u> You can creating custom config key and print out your config key value anywhere in your template.</p>
<p><code>&lt;?php echo config('your.key'); ?&gt;</code></p>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <table class="table" id="config">
        <tr>
            <td><input type="text" class="form-control" name="newKey" placeholder="Your New Config Key"></td>
            <td><input type="text" class="form-control" name="newValue" placeholder="Your New Value"></td>
        </tr>
        <?php
        global $config_file;
        $array = array(
            "google.wmt" => "hallo",
        );
        if (file_exists($config_file)) {
            $array = parse_ini_file($config_file, true);
        }
        function valueMaker($value)
        {
            if (is_string($value))
                return htmlspecialchars($value);

            if ($value === true)
                return "true";
            if ($value === false)
                return "false";

            if ($value == false)
                return "0";
            return (string)$value;
        }
        $configList = json_decode(file_get_contents('content/data/configList.json', true));
        foreach ($array as $key => $value) {
            if (!in_array($key, $configList)) {
                echo '<tr>';
                echo '<td><label for="' . $key . '">' . $key . '</label></td>';
                echo '<td><input class="form-control" type="text" id="' . $key . '" name="-config-' . $key . '" value="' . valueMaker($value) . '"></td>';
               echo '</tr>';
            }
        }
        ?>
    </table>
    <input type="submit" class="form-control btn-primary btn-sm" style="width:100px;">
</form>

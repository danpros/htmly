<h2>Your Settings:</h2>
<p>
    <u>hint:</u> Use <code>Ctrl</code>/<code>CMD⌘</code> + <code>F</code> to search for your config key or value.
</p>
<p>
    <u>pro tips:</u> You can creating custom config key and print out your config key value anywhere in your template.
</p>
<pre><code>&lt;?php echo config('your.key'); ?&gt;</code></pre>
<hr style="margin:30px 0;border:1px solid #e3e3e3;">
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <input type="submit">
    <table id="config">
        <tr>
            <td><input type="text" name="newKey" placeholder="Your New Config Key"></td>
            <td><input type="text" name="newValue" placeholder="Your New Value"></td>
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

        foreach ($array as $key => $value) {
            echo '<tr>';
            echo '<td><label for="' . $key . '">' . $key . '</label></td>';
            echo '<td><input type="text" name="-config-' . $key . '" value="' . valueMaker($value) . '"></td>';
            echo '</tr>';
        }
        ?>
    </table>
    <input type="submit">
</form>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Reservation</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
        <style>body { font-family: Arial, sans-serif; }</style>
    </head>
    <body>
        <?php
            require_once '_db.php';
            $rooms = $db->query('SELECT * FROM rooms');
            $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d\TH:i:s'); 
            $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d\TH:i:s', strtotime('+1 day')); 
        ?>
        <form id="f" action="backend_create.php" method="POST" style="padding:20px;">
            <h1>New Reservation</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="" required/></div>
            <div>Start:</div>
            <div><input type="text" id="start" name="start" value="<?php echo htmlspecialchars($start) ?>" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" value="<?php echo htmlspecialchars($end) ?>" /></div>
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php 
                        foreach ($rooms as $room) {
                            $selected = (isset($_GET['resource']) && $_GET['resource'] == $room['id']) ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div style="margin-top: 15px;"><input type="submit" value="Save" /> <a href="#">Cancel</a></div>
        </form>
        <script type="text/javascript">
            $("#f").submit(function (e) {
                e.preventDefault(); 
                var f = $("#f");
                $.ajax({
                    type: "POST",
                    url: f.attr("action"),
                    data: f.serialize(),
                    success: function(response) {
                        if (parent && parent.DayPilot && parent.DayPilot.Modal) {
                            parent.DayPilot.Modal.close({"result": "OK"});
                        } else {
                            parent.location.reload();
                        }
                    },
                    error: function() {
                        parent.location.reload();
                    }
                });
            });

            $("a:contains('Cancel')").off("click").on("click", function(e) {
                e.preventDefault();
                if (parent && parent.DayPilot && parent.DayPilot.Modal) {
                    parent.DayPilot.Modal.close();
                } else {
                    parent.location.reload();
                }
            });

            $(document).ready(function () {
                $("#name").focus();
            });
        </script>
    </body>
</html>
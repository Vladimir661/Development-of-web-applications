<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Reservation</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
        <style>body { font-family: Arial, sans-serif; }</style>
    </head>
    <body>
        <?php
            require_once '_db.php';
            $rooms = $db->query('SELECT * FROM rooms');
            
            $reservation = ['id'=>'', 'name'=>'', 'start'=>'', 'end'=>'', 'room_id'=>'', 'status'=>'New', 'paid'=>0];
            if(isset($_GET['id'])) {
                $stmt = $db->prepare('SELECT * FROM reservations WHERE id = :id');
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                $fetched = $stmt->fetch();
                if($fetched) $reservation = $fetched;
            }
        ?>
        <form id="f" action="backend_update.php" method="POST" style="padding:20px;">
            <h1>Edit Reservation</h1>
            <input type="hidden" name="id" value="<?php echo $reservation['id'] ?>" />
            
            <div>Start:</div>
            <div><input type="text" id="start" name="start" value="<?php echo $reservation['start'] ?>" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" value="<?php echo $reservation['end'] ?>" /></div>
            
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php 
                        foreach ($rooms as $room) {
                            $selected = $reservation['room_id'] == $room['id'] ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="<?php echo htmlspecialchars($reservation['name']) ?>" /></div>
            
            <div>Status:</div>
            <div>
                <select id="status" name="status">
                    <?php 
                        $options = array("New", "Confirmed", "Arrived", "CheckedOut");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['status'] ? ' selected="selected"' : '';
                            print "<option value='$option' $selected>$option</option>";
                        }
                    ?>
                </select>                
            </div>
            
            <div>Paid:</div>
            <div>
                <select id="paid" name="paid">
                    <?php 
                        $options = array(0, 50, 100);
                        foreach ($options as $option) {
                            $selected = $option == $reservation['paid'] ? ' selected="selected"' : '';
                            print "<option value='$option' $selected>$option%</option>";
                        }
                    ?>
                </select>
            </div>
            
            <div style="margin-top: 15px;"><input type="submit" value="Save" /> <a href="javascript:window.close();">Cancel</a></div>
        </form>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>New Room</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
        <style>body { font-family: Arial, sans-serif; }</style>
    </head>
    <body>
        <form id="f" action="backend_room_create.php" method="POST" style="padding:20px;">
            <h1>Add New Room</h1>
            <div>Name (e.g., Room 6): </div>
            <div><input type="text" id="name" name="name" value="" required/></div>
            
            <div style="margin-top:10px;">Capacity:</div>
            <div>
                <select id="capacity" name="capacity">
                    <option value="1">1 bed</option>
                    <option value="2">2 beds</option>
                    <option value="4">4 beds</option>
                </select>
            </div>

            <div style="margin-top:10px;">Status:</div>
            <div>
                <select id="status" name="status">
                    <option value="Ready">Ready</option>
                    <option value="Cleanup">Cleanup</option>
                    <option value="Dirty">Dirty</option>
                </select>
            </div>
            
            <div style="margin-top: 15px;"><input type="submit" value="Save" /> <a href="#">Cancel</a></div>
        </form>
        <script type="text/javascript">
            $("#f").submit(function (e) {
                e.preventDefault();
                var f = $("#f");
                $.post(f.attr("action"), f.serialize(), function (result) {
                    window.parent.location.reload();
                }).fail(function() { window.parent.location.reload(); });
            });
            $("a:contains('Cancel')").on("click", function(e) {
                e.preventDefault(); window.parent.location.reload();
            });
        </script>
    </body>
</html>
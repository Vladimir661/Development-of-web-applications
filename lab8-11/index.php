<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>HTML5 Бронювання кімнат в готелі (JavaScript/PHP/MySQL)</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    <script src="js/daypilot-all.min.js" type="text/javascript"></script>
    <style>
        .scheduler_default_rowheader_inner { border-right: 1px solid #ccc; }
        .scheduler_default_rowheader.scheduler_default_rowheadercol2 { background: #fff; }
        .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            top: 2px; bottom: 2px; left: 2px; background-color: transparent;
            border-left: 5px solid #1a9d13;
            border-right: 0px none;
        }
        .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #ea3624;
        }
        .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #f9ba25;
        }
        </style>
    </head>
    <body>
    <header>
        <div class="bg-help">
        <div class="inBox">
            <h1 id="logo">HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
            <p id="claim">AJAX'овий Календар-застосунок з JavaScript/HTML5/jQuery</p>
            <hr class="hidden" />
        </div>
        </div>
    </header>
    
    <main>
        <div style="width: 100%; padding: 20px; background: #fff; border-radius: 4px; box-sizing: border-box;">
        <div id="dp"></div>
        </div>
    </main>
    
    <div class="clear"></div>
    
    <footer>
        <address>(с) Автор лабораторної роботи: студент спеціальності КН, Зозуля В.В.</address>
    </footer>

    <script>
        var dp = new DayPilot.Scheduler("dp");
        
        dp.startDate = DayPilot.Date.today().firstDayOfMonth(); 
        dp.days = DayPilot.Date.today().daysInMonth();
        dp.scale = "Day"; 
        dp.timeHeaders = [ 
            { groupBy: "Month", format: "MMMM yyyy" },
            { groupBy: "Day", format: "d" }
        ];

        dp.rowHeaderColumns = [
            {title: "Room", width: 80},
            {title: "Capacity", width: 80},
            {title: "Status", width: 80}
        ];

        dp.onBeforeResHeaderRender = function(args) {
            var beds = function(count) { return count + " bed" + (count > 1 ? "s" : ""); };
            args.resource.columns[0].html = beds(args.resource.capacity);
            args.resource.columns[1].html = args.resource.status;
            switch (args.resource.status) {
                case "Dirty": args.resource.cssClass = "status_dirty"; break;
                case "Cleanup": args.resource.cssClass = "status_cleanup"; break;
            }
        };

dp.onTimeRangeSelected = function (args) {
            var modal = new DayPilot.Modal();
            modal.closed = function() {
                dp.clearSelection();
                loadEvents();
            };
            modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);
        };

        dp.onEventClick = function(args) {
            var modal = new DayPilot.Modal();
            modal.closed = function() {
                loadEvents();
            };
            modal.showUrl("edit.php?id=" + args.e.id());
        };

        dp.init();

        function loadResources() {
            $.post("backend_rooms.php", function(data) {
                dp.resources = data;
                dp.update();
            });
        }

        function loadEvents() {
            var start = dp.visibleStart();
            var end = dp.visibleEnd();
            $.post("backend_events.php", { start: start.toString(), end: end.toString() }, function(data) {
                dp.events.list = data;
                dp.update();
            });
        }

        loadResources();
        loadEvents();
    </script>
</body>
</html>
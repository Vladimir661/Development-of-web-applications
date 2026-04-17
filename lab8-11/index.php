<!DOCTYPE html>
<html lang="uk">
<head>
        <meta charset="UTF-8">
        <title>HTML5 Бронювання кімнат в готелі</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="js/daypilot-all.min.js"></script>
        <style>
        .scheduler_default_rowheader_inner { border-right: 1px solid #ccc; }
        .scheduler_default_rowheader.scheduler_default_rowheadercol2 { background: #fff; }
        .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner { top: 2px; bottom: 2px; left: 2px; background-color: transparent; border-left: 5px solid #1a9d13; border-right: 0px none; }
        .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner { border-left: 5px solid #ea3624; }
        .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner { border-left: 5px solid #f9ba25; }
        </style>
    </head>
    <body>
    <header>
        <div class="bg-help">
        <div class="inBox">
            <h1 id="logo">HTML5 Бронювання кімнат (Лаб 11)</h1>
            <hr class="hidden" />
        </div>
        </div>
    </header>
    
    <main>
        <div style="width: 100%; padding: 20px; background: #fff; border-radius: 4px; box-sizing: border-box;">
        
        <div style="margin-bottom: 20px; display: flex; gap: 20px; align-items: center;">
            <div>
                <label for="filter" style="font-weight: bold;">Фільтр кімнат: </label>
                <select id="filter" style="padding: 5px;">
                    <option value="0">Всі кімнати</option>
                    <option value="1">Одномісні (1 bed)</option>
                    <option value="2">Двомісні (2 beds)</option>
                    <option value="4">Сімейні (4 beds)</option>
                </select>
            </div>
            <div>
                <button id="add-room" style="padding: 6px 15px; background: #2c3e50; color: white; border: none; border-radius: 3px; cursor: pointer;">+ Додати нову кімнату</button>
            </div>
        </div>

        <div id="dp"></div>
        </div>
    </main>
    
    <footer>
        <address>(с) Автор лабораторної роботи: студент спеціальності КН, Зозуля В.В.</address>
    </footer>

    <script>
        var dp = new DayPilot.Scheduler("dp");
        
        dp.startDate = DayPilot.Date.today().firstDayOfMonth(); 
        dp.days = DayPilot.Date.today().daysInMonth();
        dp.scale = "Day"; 
        dp.timeHeaders = [ { groupBy: "Month", format: "MMMM yyyy" }, { groupBy: "Day", format: "d" } ];
        dp.rowHeaderColumns = [ {title: "Room", width: 80}, {title: "Capacity", width: 80}, {title: "Status", width: 80} ];

        dp.onBeforeResHeaderRender = function(args) {
            var beds = function(count) { return count + " bed" + (count > 1 ? "s" : ""); };
            args.resource.columns[0].html = beds(args.resource.capacity);
            args.resource.columns[1].html = args.resource.status;
            switch (args.resource.status) {
                case "Dirty": args.resource.cssClass = "status_dirty"; break;
                case "Cleanup": args.resource.cssClass = "status_cleanup"; break;
            }
        };

        dp.onBeforeEventRender = function(args) {
            var start = new DayPilot.Date(args.e.start);
            var end = new DayPilot.Date(args.e.end);
            var today = DayPilot.Date.today();
            var now = new DayPilot.Date();

            args.e.html = args.e.text + " (" + start.toString("d.MM.yyyy") + " - " + end.toString("d.MM.yyyy") + ")";

            switch (args.e.status) {
                case "New":
                    var in2days = today.addDays(1);
                    if (start < in2days) { args.e.barColor = 'red'; args.e.toolTip = 'Застаріле (не підтверджено)'; }
                    else { args.e.barColor = 'orange'; args.e.toolTip = 'Новий'; }
                    break;
                case "Confirmed":
                    var arrivalDeadline = today.addHours(18);
                    if (start < today || (start.getDatePart() === today.getDatePart() && now > arrivalDeadline)) {
                        args.e.barColor = "#f41616"; args.e.toolTip = 'Пізнє прибуття';
                    } else { args.e.barColor = "green"; args.e.toolTip = "Підтверджено"; }
                    break;
                case 'Arrived':
                    var checkoutDeadline = today.addHours(10);
                    if (end < today || (end.getDatePart() === today.getDatePart() && now > checkoutDeadline)) {
                        args.e.barColor = "#f41616"; args.e.toolTip = "Пізній виїзд";
                    } else { args.e.barColor = "#1691f4"; args.e.toolTip = "Прибув"; }
                    break;
                case 'CheckedOut':
                    args.e.barColor = "gray"; args.e.toolTip = "Виїхав";
                    break;
            }
            args.e.html = args.e.html + "<br /><span style='color:gray'>" + args.e.toolTip + "</span>";
            
            var paid = args.e.paid || 0;
            args.e.areas = [
                { bottom: 10, right: 4, html: "<div style='color:#fff; font-size: 8pt; background: rgba(0,0,0,0.5); padding: 2px;'>Paid: " + paid + "%</div>", v: "Visible"},
                { left: 0, bottom: 0, right: 0, height: 4, html: "<div style='background-color:#1a9d13; height: 100%; width:" + paid + "%'></div>", v: "Visible" }
            ];
        };

        dp.allowEventOverlap = false;
        dp.onEventMoved = function (args) {
            $.post("backend_move.php", {
                id: args.e.id(), newStart: args.newStart.toString(), newEnd: args.newEnd.toString(), newResource: args.newResource
            }, function(data) {
                dp.message(data.message);
            });
        };

        dp.eventDeleteHandling = "Update";
        dp.onEventDeleted = function(args) {
            $.post("backend_delete.php", { id: args.e.id() }, function() {
                dp.message("Deleted.");
            });
        };

        dp.onTimeRangeSelected = function (args) {
            var modal = new DayPilot.Modal();
            window.currentModal = modal;
            modal.closed = function() { dp.clearSelection(); loadEvents(); };
            modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);
        };
        dp.onEventClick = function(args) {
            var modal = new DayPilot.Modal();
            window.currentModal = modal;
            modal.closed = function() { loadEvents(); };
            modal.showUrl("edit.php?id=" + args.e.id());
        };

        dp.init();

        function loadResources() {
            $.post("backend_rooms.php", { capacity: $("#filter").val() }, function(data) {
                dp.resources = data; dp.update();
            });
        }
        function loadEvents() {
            var start = dp.visibleStart(); var end = dp.visibleEnd();
            $.post("backend_events.php", { start: start.toString(), end: end.toString() }, function(data) {
                dp.events.list = data; dp.update();
            });
        }

        $(document).ready(function() {
            $("#filter").change(function() { loadResources(); });
            
            $("#add-room").click(function() {
                var modal = new DayPilot.Modal();
                window.currentModal = modal;
                modal.closed = function() { loadResources(); };
                modal.showUrl("room_new.php");
            });
        });

        loadResources();
        loadEvents();
    </script>
</body>
</html>
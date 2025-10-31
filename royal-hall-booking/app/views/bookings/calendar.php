<div class="calendar-page">
    <div class="page-header">
        <h2>Royal Booking Calendar</h2>
        <p>View all scheduled events in the kingdom</p>
    </div>

    <!-- Month Navigation -->
    <div class="calendar-nav">
        <form method="GET" class="month-form">
            <input type="hidden" name="c" value="booking">
            <input type="hidden" name="a" value="calendar">
            
            <div class="nav-controls">
                <a href="?c=booking&a=calendar&month=<?= $month - 1 <= 0 ? 12 : $month - 1 ?>&year=<?= $month - 1 <= 0 ? $year - 1 : $year ?>" 
                   class="btn btn-secondary">⬅️ Previous</a>
                
                <select name="month" class="month-select" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                    <?php endfor; ?>
                </select>
                
                <select name="year" class="year-select" onchange="this.form.submit()">
                    <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                    <?php endfor; ?>
                </select>
                
                <a href="?c=booking&a=calendar&month=<?= $month + 1 > 12 ? 1 : $month + 1 ?>&year=<?= $month + 1 > 12 ? $year + 1 : $year ?>" 
                   class="btn btn-secondary">Next ➡️</a>
            </div>
        </form>
        
        <div class="current-month">
            <h3><?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?></h3>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar royal-card">
        <div class="calendar-header">
            <?php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; ?>
            <?php foreach ($days as $day): ?>
            <div class="calendar-day-header"><?= $day ?></div>
            <?php endforeach; ?>
        </div>
        
        <div class="calendar-body">
            <?php
            // Empty cells for days before the first day of month
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo '<div class="calendar-day empty"></div>';
            }
            
            // Days of the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $dayEvents = array_filter($events, function($event) use ($currentDate) {
                    return $event['booking_date'] == $currentDate;
                });
                
                $isToday = $currentDate == date('Y-m-d');
                $dayClass = $isToday ? 'today' : '';
                
                echo '<div class="calendar-day ' . $dayClass . '">';
                echo '<div class="day-number">' . $day . '</div>';
                
                if (!empty($dayEvents)) {
                    echo '<div class="day-events">';
                    foreach (array_slice($dayEvents, 0, 3) as $event) {
                        $statusClass = $event['status'] === 'approved' ? 'approved' : 'pending';
                        echo '<div class="calendar-event event-' . $statusClass . '">';
                        echo '<span class="event-time">' . date('g:i', strtotime($event['start_time'])) . '</span>';
                        echo '<span class="event-title">' . htmlspecialchars($event['room_name']) . '</span>';
                        echo '</div>';
                    }
                    if (count($dayEvents) > 3) {
                        echo '<div class="calendar-more">+' . (count($dayEvents) - 3) . ' more</div>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Legend -->
    <div class="calendar-legend">
        <div class="legend-item">
            <div class="legend-color event-approved"></div>
            <span>Approved Bookings</span>
        </div>
        <div class="legend-item">
            <div class="legend-color event-pending"></div>
            <span>Pending Bookings</span>
        </div>
        <div class="legend-item">
            <div class="legend-color today"></div>
            <span>Today</span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="calendar-stats">
        <div class="stat-item">
            <span class="stat-number"><?= count(array_filter($events, function($e) { return $e['status'] === 'approved'; })) ?></span>
            <span class="stat-label">Approved Events</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?= count(array_filter($events, function($e) { return $e['status'] === 'pending'; })) ?></span>
            <span class="stat-label">Pending Events</span>
        </div>
        <div class="stat-item">
            <span class="stat-number"><?= count($events) ?></span>
            <span class="stat-label">Total Events</span>
        </div>
    </div>
</div>  
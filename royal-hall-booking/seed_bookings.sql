-- Seed 20 booking records for testing
-- Make sure you're logged in as admin user first

INSERT INTO bookings (user_id, room_id, purpose, start_date, end_date, total_cost, status, created_at, updated_at) VALUES
(1, 1, 'Annual Company Meeting 2025', '2025-10-26 09:00:00', '2025-10-26 17:00:00', 2000000, 'approved', NOW(), NOW()),
(1, 2, 'Product Launch Event', '2025-10-28 13:00:00', '2025-10-28 18:00:00', 1500000, 'pending', NOW(), NOW()),
(1, 3, 'Tech Conference', '2025-11-01 08:00:00', '2025-11-01 20:00:00', 3000000, 'approved', NOW(), NOW()),
(1, 1, 'Board Meeting', '2025-11-05 10:00:00', '2025-11-05 12:00:00', 800000, 'approved', NOW(), NOW()),
(1, 2, 'Team Building Workshop', '2025-11-10 09:00:00', '2025-11-10 16:00:00', 1700000, 'pending', NOW(), NOW()),
(1, 3, 'Year End Party', '2025-12-20 18:00:00', '2025-12-21 01:00:00', 2500000, 'approved', NOW(), NOW()),
(1, 1, 'Client Presentation', '2025-11-15 14:00:00', '2025-11-15 16:00:00', 600000, 'approved', NOW(), NOW()),
(1, 2, 'Training Session', '2025-11-20 09:00:00', '2025-11-20 17:00:00', 1800000, 'approved', NOW(), NOW()),
(1, 3, 'Startup Pitch Event', '2025-11-25 13:00:00', '2025-11-25 18:00:00', 1500000, 'pending', NOW(), NOW()),
(1, 1, 'Department Meeting', '2025-12-01 10:00:00', '2025-12-01 12:00:00', 600000, 'approved', NOW(), NOW()),
(1, 2, 'Christmas Celebration', '2025-12-24 18:00:00', '2025-12-24 23:00:00', 2000000, 'approved', NOW(), NOW()),
(1, 3, 'New Year Planning', '2025-12-27 09:00:00', '2025-12-27 17:00:00', 2200000, 'pending', NOW(), NOW()),
(1, 1, 'Investor Meeting', '2026-01-05 11:00:00', '2026-01-05 13:00:00', 800000, 'approved', NOW(), NOW()),
(1, 2, 'Career Fair', '2026-01-10 08:00:00', '2026-01-10 18:00:00', 2500000, 'pending', NOW(), NOW()),
(1, 3, 'Tech Workshop', '2026-01-15 09:00:00', '2026-01-15 16:00:00', 1700000, 'approved', NOW(), NOW()),
(1, 1, 'Strategy Meeting', '2026-01-20 10:00:00', '2026-01-20 12:00:00', 600000, 'approved', NOW(), NOW()),
(1, 2, 'Sales Conference', '2026-01-25 09:00:00', '2026-01-25 17:00:00', 2000000, 'pending', NOW(), NOW()),
(1, 3, 'Leadership Training', '2026-02-01 09:00:00', '2026-02-01 16:00:00', 1800000, 'approved', NOW(), NOW()),
(1, 1, 'Project Kickoff', '2026-02-05 14:00:00', '2026-02-05 16:00:00', 600000, 'pending', NOW(), NOW()),
(1, 2, 'Innovation Summit', '2026-02-10 08:00:00', '2026-02-10 18:00:00', 2500000, 'approved', NOW(), NOW());

-- Note: Make sure the room_ids (1,2,3) exist in your rooms table
-- And user_id 1 should be your admin user
-- Adjust the total_cost based on your room pricing if needed
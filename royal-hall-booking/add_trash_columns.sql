-- Add trash-related columns to bookings table
ALTER TABLE bookings ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE;
ALTER TABLE bookings ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;
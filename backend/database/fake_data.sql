-- Insert fake users
INSERT INTO users (userID, email, password, firstName, lastName, role, profilePic) VALUES
(100, 'admin2@uob.edu.bh', '$2y$10$abcdefghijklmnopqrstuv', 'Admin', 'User', 'admin', 0x64656661756c742e6a7067),
(101, 'instructor1@uob.edu.bh', '$2y$10$abcdefghijklmnopqrstuv', 'John', 'Smith', 'instructor', 0x64656661756c742e6a7067),
(102, 'instructor2@uob.edu.bh', '$2y$10$abcdefghijklmnopqrstuv', 'Sarah', 'Johnson', 'instructor', 0x64656661756c742e6a7067),
(103, '202012345@stu.uob.edu.bh', '$2y$10$abcdefghijklmnopqrstuv', 'Mohammed', 'Ahmed', 'student', 0x64656661756c742e6a7067),
(104, '202012346@stu.uob.edu.bh', '$2y$10$abcdefghijklmnopqrstuv', 'Fatima', 'Ali', 'student', 0x64656661756c742e6a7067);

-- Insert equipment for rooms
INSERT INTO equipment (equipmentID, roomID, equipmentName) VALUES
(100, 'S40-2089', 'Projector'),
(101, 'S40-2089', 'Smart Board'),
(102, 'S40-2089', 'Computer Workstations'),
(103, 'S40-2084', 'Projector'),
(104, 'S40-2084', 'Whiteboard');

-- Insert bookings (some active, some pending, some expired)
INSERT INTO bookings (bookingID, userID, roomID, bookingTime, startTime, endTime, status, feedback) VALUES
(100, 101, 'S40-2089', '2024-12-01 08:00:00', '2024-12-10 09:00:00', '2024-12-10 11:00:00', 'active', 0),
(101, 102, 'S40-2084', '2024-12-01 10:00:00', '2024-12-11 13:00:00', '2024-12-11 15:00:00', 'pending', 0),
(102, 101, 'S40-2086', '2024-11-15 09:00:00', '2024-11-20 14:00:00', '2024-11-20 16:00:00', 'expired', 1);

-- Insert comments on bookings
INSERT INTO comments (commentID, userID, roomID, content, isRead, bookingID) VALUES
(100, 101, 'S40-2089', 'Need the room prepared with computers for programming lab', 0, 100),
(101, 102, 'S40-2084', 'Will be conducting a presentation session', 0, 101);

-- Insert replies to comments
INSERT INTO comment_reply (replyID, commentID, userID, replyContent) VALUES
(100, 100, 100, 'All computers are updated and ready for use'),
(101, 101, 100, 'Projector has been checked and is working properly');

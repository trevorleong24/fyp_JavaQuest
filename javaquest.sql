-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 30, 2025 at 07:41 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `javaquest`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

DROP TABLE IF EXISTS `administrator`;
CREATE TABLE IF NOT EXISTS `administrator` (
  `admin_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`admin_id`, `username`, `password`, `email`) VALUES
('A01', 'Admin1', 'APU123', 'A01@mail.apu.admin.my');

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
CREATE TABLE IF NOT EXISTS `answer` (
  `answer_id` varchar(50) NOT NULL,
  `answer_text` varchar(1000) NOT NULL,
  `question_id` varchar(50) NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`answer_id`, `answer_text`, `question_id`) VALUES
('A01', '.java', 'Q01'),
('A02', 'Object-Oriented', 'Q02'),
('A03', 'main()', 'Q03'),
('A04', 'Bytecode', 'Q04'),
('A05', 'myScore', 'Q05'),
('A06', '8', 'Q06'),
('A07', 'Addition', 'Q07'),
('A08', 'final', 'Q08'),
('A09', 'do-while', 'Q09'),
('A10', '==', 'Q10'),
('A11', 'boolean', 'Q11'),
('A12', 'Last unmatched if', 'Q12'),
('A13', 'switch(expression) {}', 'Q13'),
('A14', 'do-while', 'Q14'),
('A15', '3', 'Q15'),
('A16', 'int[] arr = new int[5];', 'Q16'),
('A17', '11', 'Q17'),
('A18', 'extends', 'Q18'),
('A19', 'Scanner.nextInt()', 'Q19'),
('A20', 'arr[1]', 'Q20'),
('A21', 'Garbage collection is automatic', 'Q21'),
('A22', 'do-while', 'Q22'),
('A23', 'Prevents inheritance', 'Q23'),
('A24', '5Java', 'Q24'),
('A25', 'Array index starts at 1', 'Q25'),
('A26', 'Fall-through happens', 'Q26'),
('A27', 'Constructor with no parameters', 'Q27'),
('A28', 'Scanner', 'Q28'),
('A29', 'package-private (default)', 'Q29'),
('A30', 'Belongs to the class', 'Q30');

-- --------------------------------------------------------

--
-- Table structure for table `gameprogress`
--

DROP TABLE IF EXISTS `gameprogress`;
CREATE TABLE IF NOT EXISTS `gameprogress` (
  `progress_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `quiz_id` varchar(50) NOT NULL,
  `date_taken` date NOT NULL,
  `score` int NOT NULL,
  `level_achieved` varchar(500) NOT NULL,
  `lst_played` int NOT NULL,
  PRIMARY KEY (`progress_id`),
  KEY `student_id` (`student_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gameprogress`
--

INSERT INTO `gameprogress` (`progress_id`, `student_id`, `quiz_id`, `date_taken`, `score`, `level_achieved`, `lst_played`) VALUES
('PG01', 'S01', 'QZ01', '2025-04-25', 155, 'Java Master', 100),
('PG03', 'S01', 'QZ02', '2025-04-25', 50, 'Coder', 100),
('PG05', 'S01', 'QZ03', '2025-04-25', 140, 'Java Master', 100),
('PG06', 'S01', 'QZ01', '2025-04-29', 15, 'Beginner', 4),
('PG07', 'S01', 'QZ02', '2025-04-28', 30, 'Apprentice', 8),
('PG08', 'S01', 'QZ03', '2025-04-25', 20, 'Beginner', 4);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `question_id` varchar(50) NOT NULL,
  `question_text` varchar(1000) NOT NULL,
  `Option 1` varchar(255) NOT NULL,
  `Option 2` varchar(255) NOT NULL,
  `Option 3` varchar(255) NOT NULL,
  `Option 4` varchar(255) NOT NULL,
  `quiz_id` varchar(50) NOT NULL,
  PRIMARY KEY (`question_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`question_id`, `question_text`, `Option 1`, `Option 2`, `Option 3`, `Option 4`, `quiz_id`) VALUES
('Q01', 'What is the correct extension for a Java file?', '.class', '.java', '.jav', '.exe', 'QZ01'),
('Q02', 'Java is mainly a ____ programming language.', 'Procedural', 'Object-Oriented', 'Scripting', 'Functional', 'QZ01'),
('Q03', 'Which method is the entry point of a Java program?', 'start()', 'run()', 'main()', 'begin()', 'QZ01'),
('Q04', 'Java source code is first compiled into ___.', 'Macine Code', 'Bytecode', 'Assembly', 'Text', 'QZ01'),
('Q05', 'Which is a valid Java identifier?', '123name', '@score', 'myScore', '#age', 'QZ01'),
('Q06', 'How many primitive data types are there in Java?', '4', '6', '8', '10', 'QZ01'),
('Q07', 'The \"+\" operator in Java is used for:', 'Multiplication', 'Addition', 'Subtraction', 'Division', 'QZ01'),
('Q08', 'What keyword is used to create a constant value?', 'static', 'const', 'final', 'fixed', 'QZ01'),
('Q09', 'Which loop checks the condition after executing the body once?', 'for', 'while', 'do-while', 'switch', 'QZ01'),
('Q10', 'What is the symbol for \"equal to\" in Java?', '=', '==', '===', '!=', 'QZ01'),
('Q11', 'Which data type is most appropriate to store true/false values?', 'int', 'char', 'boolean', 'byte', 'QZ02'),
('Q12', 'In an if-else statement, else belongs to which if?', 'First if', 'Last unmatched if', 'Random if', 'No specific rule', 'QZ02'),
('Q13', 'What is the correct syntax for a switch statement?', 'switch case()', 'switch {}', 'switch(expression) {}', 'switch(expression) []', 'QZ02'),
('Q14', 'In Java, which loop will always execute at least once?', 'for', 'while', 'do-while', 'foreach', 'QZ02'),
('Q15', 'What will be the output of this code?\r\n\r\nSystem.out.println(10/3);', '3.33', '3', '4', '0', 'QZ02'),
('Q16', 'How can you create an array of integers in Java?', 'int[] arr = new int[5];', 'array int[5];', 'int arr[5];', 'array arr = new int[5];', 'QZ02'),
('Q17', 'What is the result of 5 + 2 * 3?', '21', '11', '9', '7', 'QZ02'),
('Q18', 'What keyword is used to inherit a class in Java?', 'implements', 'extends', 'inherits', 'imports', 'QZ02'),
('Q19', 'Which method is used to take user input in Java?', 'Scanner.nextInt()', 'input()', 'read()', 'get()', 'QZ02'),
('Q20', 'How do you access the second element of an array named arr?', 'arr[2]', 'arr[1]', 'arr(2)', 'arr.get(2)', 'QZ02'),
('Q21', 'What is true about Java memory management?', 'Programmer frees memory manually', 'Garbage collection is automatic', 'Memory leaks often happen', 'You use \"delete\" to free memory', 'QZ03'),
('Q22', 'Which loop structure guarantees a minimum of one iteration?', 'while', 'do-while', 'for', 'foreach', 'QZ03'),
('Q23', 'Which of the following is NOT a benefit of encapsulation?', 'Improved security', 'Better modularity', 'Prevents inheritance', 'Easier maintenance', 'QZ03'),
('Q24', 'What is the output?\r\nSystem.out.println(2 + 3 + \"Java\");', '5Java', '23Java', 'Java23', 'Error', 'QZ03'),
('Q25', 'Which statement about arrays is false?', 'Arrays have fixed size', 'Array index starts at 1', 'Arrays can store primitives', 'Arrays can store objects', 'QZ03'),
('Q26', 'In a switch statement, if no break is used:', 'Only matching case runs', 'Fall-through happens', 'Program crashes', 'No output', 'QZ03'),
('Q27', 'What is the default constructor?', 'Constructor with parameters', 'User-defined constructor', 'Constructor with no parameters', 'None', 'QZ03'),
('Q28', 'Which class allows reading user input?', 'Scanner', 'Reader', 'Writer', 'Printer', 'QZ03'),
('Q29', 'What is the access level of members when no modifier is specified?', 'private', 'protected', 'public', 'package-private (default)', 'QZ03'),
('Q30', 'If a method is static, it:', 'Cannot be called', 'Belongs to the object', 'Belongs to the class', 'Cannot have parameters', 'QZ03');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
CREATE TABLE IF NOT EXISTS `quiz` (
  `quiz_id` varchar(50) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  PRIMARY KEY (`quiz_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `title`, `description`, `admin_id`) VALUES
('QZ01', 'Easy', 'This level focuses on the fundamentals of Java programming. Questions are designed to test basic understanding of syntax, data types, operators, and simple Java constructs. Suitable for beginners who are just starting their journey into programming.', 'A01'),
('QZ02', 'Normal', 'This level challenges students to apply their basic knowledge to real code scenarios. It includes logic-based questions, use of control structures (if-else, loops), and array manipulation. Perfect for learners who are growing confident with the basics and ready to explore deeper logic.', 'A01'),
('QZ03', 'Hard', 'The hard level is for students who want to test their deep understanding of Java. It includes questions involving flow control, nested conditions, tricky syntax, object-oriented programming, and more. Ideal for those aiming to strengthen their problem-solving and critical thinking in Java.', 'A01');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `student_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  PRIMARY KEY (`student_id`) USING BTREE,
  UNIQUE KEY `username` (`username`,`email`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `username`, `password`, `email`, `dob`, `admin_id`, `gender`) VALUES
('S01', 'marc', '12345', 'S01@gmail.com', '2004-07-28', 'A01', 'Male'),
('S02', 'eve', '12345', 'S02@gmail.com', '2005-04-12', 'A01', 'Female'),
('S03', 'taro', '12345', 'S03@gmail.com', '2006-05-17', 'A01', 'Male');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `gameprogress`
--
ALTER TABLE `gameprogress`
  ADD CONSTRAINT `gameprogress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gameprogress_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `admin_id` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `administrator` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

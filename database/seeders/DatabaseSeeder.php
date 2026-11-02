<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive academic data, B.Tech subjects,
     * and a minimum of 10-12 questions per examination.
     */
    public function run(): void
    {
        // 1. Initial Administrator: Prof. M. Bose (Admin ID: 01234, Password: password123)
        $admin = User::updateOrCreate(
            ['admin_id' => '01234'],
            [
                'name' => 'Prof. M. Bose (Admin)',
                'email' => 'prof.m.bose@examportal.edu',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Academic Batches:
        // - BTech CSE Section A
        // - BTech CSE Section B
        // - BCA Section B
        // - BCA Section C
        $batch1 = Batch::updateOrCreate(
            [
                'course_name' => 'BTech CSE',
                'semester' => 'Semester 1',
                'section' => 'Section A',
                'academic_year' => '2025-2026',
            ],
            [
                'department' => 'Computer Science & Engineering',
            ]
        );

        $batch2 = Batch::updateOrCreate(
            [
                'course_name' => 'BTech CSE',
                'semester' => 'Semester 1',
                'section' => 'Section B',
                'academic_year' => '2025-2026',
            ],
            [
                'department' => 'Computer Science & Engineering',
            ]
        );

        $batch3 = Batch::updateOrCreate(
            [
                'course_name' => 'BCA',
                'semester' => 'Semester 1',
                'section' => 'Section B',
                'academic_year' => '2025-2026',
            ],
            [
                'department' => 'Computer Applications',
            ]
        );

        $batch4 = Batch::updateOrCreate(
            [
                'course_name' => 'BCA',
                'semester' => 'Semester 1',
                'section' => 'Section C',
                'academic_year' => '2025-2026',
            ],
            [
                'department' => 'Computer Applications',
            ]
        );

        // Clean up any legacy mock batch
        Batch::where('course_name', 'BTech ECE')->delete();

        // 3. Initial Students (Password: password123)
        $student1 = User::updateOrCreate(
            ['email' => 'abir.banerjee@student.edu'],
            [
                'name' => 'Abir Banerjee',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => 'STU2025001',
                'batch_id' => $batch1->id,
                'is_active' => true,
            ]
        );

        $student2 = User::updateOrCreate(
            ['email' => 'bipasha.sarkar@student.edu'],
            [
                'name' => 'Bipasha Sarkar',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => 'STU2025002',
                'batch_id' => $batch1->id,
                'is_active' => true,
            ]
        );

        $student3 = User::updateOrCreate(
            ['email' => 'dinesh.kumar@student.edu'],
            [
                'name' => 'Dinesh Kumar',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => 'STU2025003',
                'batch_id' => $batch2->id,
                'is_active' => true,
            ]
        );

        // Remove legacy mock students and old exams with under 10 questions if they exist
        User::whereIn('email', ['john.doe@student.edu', 'jane.smith@student.edu', 'alex.kumar@student.edu'])->delete();
        Exam::whereIn('title', [
            'Terminal Assessment: Web Technology & Client-Side Scripting',
            'Diagnostic Test: Computer Fundamentals & Digital Logic',
            'Quiz 2: Software Engineering & Agile Principles',
        ])->each(function ($e) {
            $e->questions()->delete();
            $e->delete();
        });

        // Current date string for instant demo availability
        $today = now()->format('Y-m-d');

        // =========================================================================
        // Test 1: Mid-Semester Exam: Fundamentals of Programming & C (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam1 = Exam::updateOrCreate(
            [
                'title' => 'Mid-Semester Exam: Fundamentals of Programming & C',
                'batch_id' => $batch1->id,
            ],
            [
                'subject' => 'CS101 - Introduction to Programming & Problem Solving',
                'instructions' => "1. This examination contains 12 multiple-choice questions on C programming.\n2. Each question carries 2 marks. No negative marks.\n3. Click 'Save Answer' after choosing an option.\n4. When you have answered all questions or your time is up, click 'Submit Exam'.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 30,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam1Questions = [
            [
                'question_text' => 'Which of the following is the correct extension for a standard C language source code file?',
                'option_a' => '.cpp',
                'option_b' => '.c',
                'option_c' => '.class',
                'option_d' => '.java',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which standard header file in C is required for performing input and output operations like printf() and scanf()?',
                'option_a' => 'stdlib.h',
                'option_b' => 'math.h',
                'option_c' => 'stdio.h',
                'option_d' => 'conio.h',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the format specifier used in printf() to display an integer value in C?',
                'option_a' => '%d',
                'option_b' => '%f',
                'option_c' => '%c',
                'option_d' => '%s',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which data structure follows the Last-In, First-Out (LIFO) principle?',
                'option_a' => 'Queue',
                'option_b' => 'Stack',
                'option_c' => 'Linked List',
                'option_d' => 'Binary Tree',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which keyword is used in C to prevent any modification to a variable after initialization?',
                'option_a' => 'static',
                'option_b' => 'volatile',
                'option_c' => 'const',
                'option_d' => 'register',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which operator is used in C to obtain the memory address of an existing variable?',
                'option_a' => '*',
                'option_b' => '->',
                'option_c' => '&',
                'option_d' => '%',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which standard library function dynamically allocates memory on the heap in C without initializing the allocated bytes?',
                'option_a' => 'calloc()',
                'option_b' => 'malloc()',
                'option_c' => 'realloc()',
                'option_d' => 'alloc()',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the return type of the malloc() function in C when allocation succeeds?',
                'option_a' => 'int*',
                'option_b' => 'char*',
                'option_c' => 'void*',
                'option_d' => 'null*',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which function is used in C to release dynamically allocated memory back to the operating system heap?',
                'option_a' => 'delete()',
                'option_b' => 'remove()',
                'option_c' => 'free()',
                'option_d' => 'dealloc()',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What will be the output of evaluating the expression (5 / 2) using integer division in C?',
                'option_a' => '2.5',
                'option_b' => '2',
                'option_c' => '3',
                'option_d' => '2.0',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which escape sequence represents a newline character in C strings and printf statements?',
                'option_a' => '\\t',
                'option_b' => '\\r',
                'option_c' => '\\n',
                'option_d' => '\\0',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the index of the first element in any one-dimensional C array?',
                'option_a' => '1',
                'option_b' => '-1',
                'option_c' => '0',
                'option_d' => 'Dependent on compiler flag',
                'correct_option' => 'C',
            ],
        ];

        foreach ($exam1Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam1->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 2: Continuous Assessment: Database Management Systems & SQL (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam2 = Exam::updateOrCreate(
            [
                'title' => 'Continuous Assessment: Database Management Systems & SQL',
                'batch_id' => $batch1->id,
            ],
            [
                'subject' => 'CS201 - Relational Database Systems & SQL Engineering',
                'instructions' => "1. All 12 questions are compulsory.\n2. Each correct answer carries 2 marks.\n3. The test evaluates SQL queries, Normalization, and ACID transaction properties.\n4. Click 'Save Answer' after every selection.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 40,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam2Questions = [
            [
                'question_text' => 'Which SQL clause is used to filter records based on a specified search condition in a SELECT query?',
                'option_a' => 'ORDER BY',
                'option_b' => 'GROUP BY',
                'option_c' => 'WHERE',
                'option_d' => 'HAVING',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In relational database transaction management, what does the acronym ACID stand for?',
                'option_a' => 'Accuracy, Consistency, Integrity, Durability',
                'option_b' => 'Atomicity, Consistency, Isolation, Durability',
                'option_c' => 'Atomicity, Concurrency, Indexing, Data',
                'option_d' => 'Authentication, Concurrency, Isolation, Durability',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which of the following SQL statements belongs to the Data Definition Language (DDL) category?',
                'option_a' => 'SELECT',
                'option_b' => 'INSERT',
                'option_c' => 'UPDATE',
                'option_d' => 'CREATE',
                'correct_option' => 'D',
            ],
            [
                'question_text' => 'Which column attribute or constraint guarantees that no two rows can have duplicate values in that column and cannot contain NULL?',
                'option_a' => 'FOREIGN KEY',
                'option_b' => 'PRIMARY KEY',
                'option_c' => 'CHECK',
                'option_d' => 'DEFAULT',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which normal form eliminates transitive functional dependencies for non-prime attributes?',
                'option_a' => 'First Normal Form (1NF)',
                'option_b' => 'Second Normal Form (2NF)',
                'option_c' => 'Third Normal Form (3NF)',
                'option_d' => 'Boyce-Codd Normal Form (BCNF)',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which SQL clause is used to filter summarized groups created by a GROUP BY clause?',
                'option_a' => 'WHERE',
                'option_b' => 'FILTER BY',
                'option_c' => 'HAVING',
                'option_d' => 'ORDER BY',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which JOIN returns all records from the left table, and the matched records from the right table (with NULLs for non-matches)?',
                'option_a' => 'INNER JOIN',
                'option_b' => 'LEFT JOIN (LEFT OUTER JOIN)',
                'option_c' => 'RIGHT JOIN (RIGHT OUTER JOIN)',
                'option_d' => 'CROSS JOIN',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which data structure is most commonly implemented for high-performance indexing in modern relational databases?',
                'option_a' => 'Linked List',
                'option_b' => 'Hash Table',
                'option_c' => 'B+ Tree',
                'option_d' => 'Binary Heap',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which SQL command is issued to discard all uncommitted changes performed in the current database transaction?',
                'option_a' => 'COMMIT',
                'option_b' => 'SAVEPOINT',
                'option_c' => 'ROLLBACK',
                'option_d' => 'REVOKE',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What type of database key creates a link between records in two distinct relational tables to enforce referential integrity?',
                'option_a' => 'Candidate Key',
                'option_b' => 'Super Key',
                'option_c' => 'Foreign Key',
                'option_d' => 'Composite Key',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which of the following aggregate functions ignores NULL values when computing its result in SQL?',
                'option_a' => 'COUNT(*)',
                'option_b' => 'AVG(column_name)',
                'option_c' => 'NVL()',
                'option_d' => 'COALESCE()',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which property in database ACID transactions ensures that once a transaction commits, its effects survive system crashes or power failures?',
                'option_a' => 'Atomicity',
                'option_b' => 'Consistency',
                'option_c' => 'Isolation',
                'option_d' => 'Durability',
                'correct_option' => 'D',
            ],
        ];

        foreach ($exam2Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam2->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 3: Mid-Semester Assessment: Object-Oriented Programming with Java (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam3 = Exam::updateOrCreate(
            [
                'title' => 'Mid-Semester Assessment: Object-Oriented Programming with Java',
                'batch_id' => $batch2->id,
            ],
            [
                'subject' => 'CS102 - Object-Oriented Design & Java Platform',
                'instructions' => "1. 12 Multiple Choice Questions testing core Java OOP concepts, exceptions, and memory.\n2. 2 marks per question.\n3. Make sure to review your answers before submitting.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 40,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam3Questions = [
            [
                'question_text' => 'Which OOP principle is implemented in Java by making fields private and providing public getter and setter methods?',
                'option_a' => 'Inheritance',
                'option_b' => 'Polymorphism',
                'option_c' => 'Encapsulation',
                'option_d' => 'Abstraction',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In Java, which keyword is used by a child class to inherit state and behavior from a parent superclass?',
                'option_a' => 'implements',
                'option_b' => 'extends',
                'option_c' => 'inherits',
                'option_d' => 'super',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the standard memory size allocated for an int primitive data type in the Java Virtual Machine (JVM)?',
                'option_a' => '16 bits (2 bytes)',
                'option_b' => '32 bits (4 bytes)',
                'option_c' => '64 bits (8 bytes)',
                'option_d' => '8 bits (1 byte)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which of the following access modifiers CANNOT be applied to an outer, top-level Java class declaration?',
                'option_a' => 'public',
                'option_b' => 'package-private (default)',
                'option_c' => 'private',
                'option_d' => 'final',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which method signature is the official entry point for JVM execution of a standalone Java application?',
                'option_a' => 'public void main(String args[])',
                'option_b' => 'public static int main(String[] args)',
                'option_c' => 'public static void main(String[] args)',
                'option_d' => 'private static void main(String[] args)',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which keyword in Java is used inside a subclass constructor to explicitly invoke the constructor of its superclass?',
                'option_a' => 'this()',
                'option_b' => 'super()',
                'option_c' => 'parent()',
                'option_d' => 'base()',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is an interface that declares exactly one abstract method called in Java 8 and newer?',
                'option_a' => 'Marker Interface',
                'option_b' => 'Abstract Interface',
                'option_c' => 'Functional Interface',
                'option_d' => 'Single Interface',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the superclass of all classes in the Java programming language from which every class implicitly inherits?',
                'option_a' => 'java.lang.Class',
                'option_b' => 'java.lang.Object',
                'option_c' => 'java.lang.System',
                'option_d' => 'java.lang.Base',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which keyword is used in Java to prevent a variable from being re-assigned, a method from being overridden, or a class from being subclassed?',
                'option_a' => 'static',
                'option_b' => 'abstract',
                'option_c' => 'final',
                'option_d' => 'immutable',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which runtime exception is automatically thrown by the JVM when attempting to divide an integer by zero?',
                'option_a' => 'NullPointerException',
                'option_b' => 'IllegalArgumentException',
                'option_c' => 'ArithmeticException',
                'option_d' => 'NumberFormatException',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which class in the Java Collections Framework implements a resizable dynamic array?',
                'option_a' => 'LinkedList',
                'option_b' => 'ArrayList',
                'option_c' => 'HashSet',
                'option_d' => 'TreeSet',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Where in JVM memory are objects and instances created using the "new" keyword allocated?',
                'option_a' => 'Stack Memory',
                'option_b' => 'Heap Memory',
                'option_c' => 'Method Area / Metaspace',
                'option_d' => 'PC Register',
                'correct_option' => 'B',
            ],
        ];

        foreach ($exam3Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam3->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 4: Major Assessment: Data Structures & Algorithm Analysis (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam4 = Exam::updateOrCreate(
            [
                'title' => 'Major Assessment: Data Structures & Algorithm Analysis',
                'batch_id' => $batch1->id,
            ],
            [
                'subject' => 'CS202 - Data Structures, Trees, Graphs & Complexity',
                'instructions' => "1. 12 comprehensive questions on Trees, Graphs, Sorting algorithms, and Asymptotic Complexity.\n2. Duration: 45 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 45,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam4Questions = [
            [
                'question_text' => 'What is the worst-case time complexity of the standard QuickSort algorithm when the chosen pivot is always the smallest or largest element?',
                'option_a' => 'O(n log n)',
                'option_b' => 'O(n)',
                'option_c' => 'O(n²)',
                'option_d' => 'O(log n)',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the worst-case time complexity of performing binary search on a sorted array of n elements?',
                'option_a' => 'O(1)',
                'option_b' => 'O(log n)',
                'option_c' => 'O(n)',
                'option_d' => 'O(n log n)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which data structure is fundamentally used to implement Breadth-First Search (BFS) graph traversal?',
                'option_a' => 'Stack',
                'option_b' => 'Queue',
                'option_c' => 'Priority Queue',
                'option_d' => 'Binary Search Tree',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which data structure is fundamentally used for Depth-First Search (DFS) graph traversal or call stack recursion?',
                'option_a' => 'Stack',
                'option_b' => 'Queue',
                'option_c' => 'Linked List',
                'option_d' => 'Hash Map',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'What is the minimum number of standard queues required to simulate the behavior of a LIFO Stack?',
                'option_a' => '1',
                'option_b' => '2',
                'option_c' => '3',
                'option_d' => '4',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the maximum height of a balanced Binary Search Tree (AVL Tree) containing n nodes?',
                'option_a' => 'O(1)',
                'option_b' => 'O(log n)',
                'option_c' => 'O(n)',
                'option_d' => 'O(n²)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which algorithm solves the single-source shortest path problem on a directed graph with non-negative edge weights?',
                'option_a' => 'Kruskal\'s Algorithm',
                'option_b' => 'Prim\'s Algorithm',
                'option_c' => 'Dijkstra\'s Algorithm',
                'option_d' => 'Floyd-Warshall Algorithm',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In a Max-Heap data structure with n distinct elements, where is the element with the maximum key value guaranteed to be located?',
                'option_a' => 'At any leaf node',
                'option_b' => 'At the root node',
                'option_c' => 'At index n/2',
                'option_d' => 'At the rightmost leaf',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which sorting algorithm has a guaranteed worst-case time complexity of O(n log n) and is also a stable sorting algorithm?',
                'option_a' => 'QuickSort',
                'option_b' => 'HeapSort',
                'option_c' => 'MergeSort',
                'option_d' => 'SelectionSort',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In a circular queue of capacity SIZE implemented using an array with front and rear pointers, which condition indicates that the queue is FULL?',
                'option_a' => 'front == rear',
                'option_b' => '(rear + 1) % SIZE == front',
                'option_c' => 'rear == SIZE - 1',
                'option_d' => 'front == (rear + SIZE) % 2',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which algorithm finds a Minimum Spanning Tree (MST) of a weighted graph by growing the tree one vertex at a time from a starting root?',
                'option_a' => 'Prim\'s Algorithm',
                'option_b' => 'Kruskal\'s Algorithm',
                'option_c' => 'Bellman-Ford Algorithm',
                'option_d' => 'Dijkstra\'s Algorithm',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'What is the Reverse Polish (Postfix) representation of the infix arithmetic expression: (A + B) * C?',
                'option_a' => '+ A B * C',
                'option_b' => 'A B + C *',
                'option_c' => 'A B C + *',
                'option_d' => '* + A B C',
                'correct_option' => 'B',
            ],
        ];

        foreach ($exam4Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam4->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 5: Terminal Examination: Operating Systems & Process Management (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam5 = Exam::updateOrCreate(
            [
                'title' => 'Terminal Examination: Operating Systems & Process Management',
                'batch_id' => $batch2->id,
            ],
            [
                'subject' => 'CS203 - Operating Systems Architecture & Process Management',
                'instructions' => "1. 12 questions on Process Scheduling, Semaphores, Deadlocks, and Paging.\n2. Duration: 40 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 40,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam5Questions = [
            [
                'question_text' => 'Which CPU scheduling algorithm is non-preemptive and notorious for suffering from the "Convoy Effect"?',
                'option_a' => 'Shortest Job First (SJF)',
                'option_b' => 'First-Come, First-Served (FCFS)',
                'option_c' => 'Round Robin (RR)',
                'option_d' => 'Multi-Level Feedback Queue',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which of the following is NOT one of the four necessary Coffman conditions required for a system deadlock to occur?',
                'option_a' => 'Mutual Exclusion',
                'option_b' => 'Hold and Wait',
                'option_c' => 'Circular Wait',
                'option_d' => 'Preemption of resources allowed',
                'correct_option' => 'D',
            ],
            [
                'question_text' => 'What is the primary function of the Translation Lookaside Buffer (TLB) in modern hardware memory management?',
                'option_a' => 'A hardware cache for fast virtual-to-physical address translations',
                'option_b' => 'A software table holding operating system process IDs',
                'option_c' => 'A buffer storing disk blocks before writing to secondary storage',
                'option_d' => 'A register maintaining thread program counters',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which page replacement algorithm can experience Belady\'s Anomaly (where increasing page frames leads to more page faults)?',
                'option_a' => 'Optimal (OPT)',
                'option_b' => 'Least Recently Used (LRU)',
                'option_c' => 'First-In, First-Out (FIFO)',
                'option_d' => 'Least Frequently Used (LFU)',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which algorithm is utilized by operating systems for deadlock avoidance by verifying that resource allocations maintain a safe state?',
                'option_a' => 'Round Robin Algorithm',
                'option_b' => 'Banker\'s Algorithm',
                'option_c' => 'Elevator Algorithm',
                'option_d' => 'Peterson\'s Algorithm',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which POSIX system call in UNIX/Linux creates an exact duplicate copy of the calling process as a new child process?',
                'option_a' => 'exec()',
                'option_b' => 'fork()',
                'option_c' => 'spawn()',
                'option_d' => 'clone()',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which synchronization mechanism provides atomic wait() (P) and signal() (V) operations on an integer variable?',
                'option_a' => 'Monitor',
                'option_b' => 'Semaphore',
                'option_c' => 'Spinlock',
                'option_d' => 'Barrier',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'When a running process initiates an I/O disk read operation, which process state transition occurs immediately?',
                'option_a' => 'Running to Ready',
                'option_b' => 'Running to Waiting (Blocked)',
                'option_c' => 'Waiting to Terminated',
                'option_d' => 'Ready to Running',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the phenomenon called when the operating system spends more time servicing page faults than executing user instructions?',
                'option_a' => 'Pipelining',
                'option_b' => 'Thrashing',
                'option_c' => 'Starvation',
                'option_d' => 'Fragmentation',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the section of code called where shared resources or variables are accessed and mutual exclusion must be enforced?',
                'option_a' => 'Race Condition',
                'option_b' => 'Entry Section',
                'option_c' => 'Critical Section',
                'option_d' => 'Remainder Section',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which of the following is shared among all peer threads belonging to the same operating system process?',
                'option_a' => 'Call stack',
                'option_b' => 'CPU register state',
                'option_c' => 'Heap memory and global data variables',
                'option_d' => 'Program counter value',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In Round Robin (RR) CPU scheduling, what is the fixed time slice allocated to each process before preemption called?',
                'option_a' => 'Context switch time',
                'option_b' => 'Time Quantum',
                'option_c' => 'Burst cycle',
                'option_d' => 'Latency window',
                'correct_option' => 'B',
            ],
        ];

        foreach ($exam5Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam5->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 6: Comprehensive Assessment: Computer Networks & Internet Protocols (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam6 = Exam::updateOrCreate(
            [
                'title' => 'Comprehensive Assessment: Computer Networks & Internet Protocols',
                'batch_id' => $batch1->id,
            ],
            [
                'subject' => 'CS204 - Computer Networks & Data Communications',
                'instructions' => "1. 12 MCQs covering OSI layers, TCP/IP, IP addressing, Routing, and DNS.\n2. Duration: 40 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 40,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam6Questions = [
            [
                'question_text' => 'Which layer of the standard OSI 7-layer reference model is responsible for end-to-end reliability, flow control, and port multiplexing?',
                'option_a' => 'Network Layer',
                'option_b' => 'Data Link Layer',
                'option_c' => 'Transport Layer',
                'option_d' => 'Session Layer',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the length in bits of a standard IPv4 address compared to an IPv6 address?',
                'option_a' => '16 bits vs 64 bits',
                'option_b' => '32 bits vs 128 bits',
                'option_c' => '64 bits vs 256 bits',
                'option_d' => '32 bits vs 64 bits',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which network protocol dynamically assigns IP addresses, subnet masks, and default gateways to client devices joining a LAN?',
                'option_a' => 'DNS',
                'option_b' => 'DHCP',
                'option_c' => 'ARP',
                'option_d' => 'ICMP',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the standard destination port number used by web servers for secure HTTPS encrypted communication?',
                'option_a' => '80',
                'option_b' => '21',
                'option_c' => '443',
                'option_d' => '8080',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which protocol is connection-oriented, guarantees packet ordering, and performs retransmissions for lost segments?',
                'option_a' => 'UDP (User Datagram Protocol)',
                'option_b' => 'TCP (Transmission Control Protocol)',
                'option_c' => 'IP (Internet Protocol)',
                'option_d' => 'ICMP (Internet Control Message Protocol)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which protocol resolves a known 32-bit IPv4 logical address to a 48-bit physical MAC hardware address on the local network?',
                'option_a' => 'DNS',
                'option_b' => 'RARP',
                'option_c' => 'ARP (Address Resolution Protocol)',
                'option_d' => 'NAT',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the standard subnet mask in dotted-decimal notation for an IPv4 CIDR prefix of /24?',
                'option_a' => '255.0.0.0',
                'option_b' => '255.255.0.0',
                'option_c' => '255.255.255.0',
                'option_d' => '255.255.255.128',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which interior gateway routing protocol is an implementation of the Link-State routing algorithm using Dijkstra shortest-path calculations?',
                'option_a' => 'RIP (Routing Information Protocol)',
                'option_b' => 'OSPF (Open Shortest Path First)',
                'option_c' => 'BGP (Border Gateway Protocol)',
                'option_d' => 'EGP (Exterior Gateway Protocol)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which IPv4 header field is decremented by 1 at every router hop to prevent packets from endlessly looping through the network?',
                'option_a' => 'Checksum',
                'option_b' => 'Time to Live (TTL)',
                'option_c' => 'Identification',
                'option_d' => 'Flags',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What system translates human-friendly Internet domain names (such as google.com) into computer-routable numerical IP addresses?',
                'option_a' => 'DNS (Domain Name System)',
                'option_b' => 'WINS',
                'option_c' => 'NTP',
                'option_d' => 'SMTP',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which TCP control flag is set in the very first segment transmitted to initiate the 3-way connection establishment handshake?',
                'option_a' => 'ACK',
                'option_b' => 'SYN',
                'option_c' => 'FIN',
                'option_d' => 'RST',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'At which layer of the OSI model does a standard network router operate when inspecting packet IP destination headers?',
                'option_a' => 'Layer 1 - Physical Layer',
                'option_b' => 'Layer 2 - Data Link Layer',
                'option_c' => 'Layer 3 - Network Layer',
                'option_d' => 'Layer 4 - Transport Layer',
                'correct_option' => 'C',
            ],
        ];

        foreach ($exam6Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam6->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 7: Core Evaluation: Computer Organization & Architecture (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam7 = Exam::updateOrCreate(
            [
                'title' => 'Core Evaluation: Computer Organization & Architecture',
                'batch_id' => $batch2->id,
            ],
            [
                'subject' => 'CS205 - Computer Organization & Microprocessor Design',
                'instructions' => "1. 12 questions on Instruction Pipelining, Cache Mapping, Hazards, and Bus Architectures.\n2. Duration: 35 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 35,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam7Questions = [
            [
                'question_text' => 'What is the hallmark architectural feature of the classical Von Neumann computer architecture model?',
                'option_a' => 'Physically separate memory units for instructions and data',
                'option_b' => 'A single shared memory space storing both program instructions and data',
                'option_c' => 'Elimination of the central processing unit',
                'option_d' => 'Direct connection between all I/O devices without buses',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which dedicated CPU register holds the memory address of the next machine instruction to be fetched from RAM?',
                'option_a' => 'Instruction Register (IR)',
                'option_b' => 'Memory Data Register (MDR)',
                'option_c' => 'Program Counter (PC)',
                'option_d' => 'Accumulator (AC)',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In CPU instruction pipelining, what type of hazard occurs when an instruction requires the result of a preceding instruction that has not completed execution?',
                'option_a' => 'Structural Hazard',
                'option_b' => 'Data Hazard',
                'option_c' => 'Control Hazard',
                'option_d' => 'Branch Hazard',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which cache mapping technique allows any given main memory block to be placed into any arbitrary cache line slot?',
                'option_a' => 'Direct Mapping',
                'option_b' => 'Associative Mapping (Fully Associative)',
                'option_c' => 'Set-Associative Mapping',
                'option_d' => 'Sector Mapping',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which of the following is a primary design characteristic of Reduced Instruction Set Computer (RISC) processors?',
                'option_a' => 'Variable-length instruction formats',
                'option_b' => 'Large instruction set with complex multi-cycle addressing modes',
                'option_c' => 'Fixed-length instructions with simple single-cycle register-to-register operations',
                'option_d' => 'Extensive microprogramming in hardware control unit',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which component inside the central processing unit generates control signals to coordinate instruction fetching, decoding, and execution?',
                'option_a' => 'Arithmetic Logic Unit (ALU)',
                'option_b' => 'Control Unit (CU)',
                'option_c' => 'General Purpose Registers',
                'option_d' => 'Bus Interface Unit',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the fastest storage element in the entire computer memory hierarchy with single clock-cycle access speed?',
                'option_a' => 'L1 Cache',
                'option_b' => 'CPU Registers',
                'option_c' => 'Static RAM (SRAM)',
                'option_d' => 'Synchronous DRAM (SDRAM)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What type of processor interrupt is generated internally by program software execution conditions, such as division by zero or invalid opcode?',
                'option_a' => 'Hardware Maskable Interrupt',
                'option_b' => 'Trap (Software Exception)',
                'option_c' => 'Non-Maskable Interrupt (NMI)',
                'option_d' => 'DMA Transfer Complete',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which technique enables high-speed peripheral devices (like NVMe SSDs or NICs) to transfer data blocks directly to and from main RAM without passing through the CPU?',
                'option_a' => 'Programmed I/O',
                'option_b' => 'Interrupt-Driven I/O',
                'option_c' => 'Direct Memory Access (DMA)',
                'option_d' => 'Memory Banking',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which memory locality principle states that if a memory location is accessed once, locations with nearby memory addresses are likely to be accessed soon?',
                'option_a' => 'Temporal Locality',
                'option_b' => 'Spatial Locality',
                'option_c' => 'Branch Locality',
                'option_d' => 'Associative Locality',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which system bus carries memory address references originating from the CPU to main memory modules and I/O controllers?',
                'option_a' => 'Data Bus',
                'option_b' => 'Control Bus',
                'option_c' => 'Address Bus',
                'option_d' => 'Peripheral Bus',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which arithmetic representation for negative signed integers is universally adopted in modern microprocessor ALUs because addition and subtraction use identical circuitry?',
                'option_a' => 'Signed Magnitude',
                'option_b' => 'One\'s Complement',
                'option_c' => 'Two\'s Complement',
                'option_d' => 'Biased / Excess-K',
                'correct_option' => 'C',
            ],
        ];

        foreach ($exam7Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam7->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 8: End-Semester Evaluation: Software Engineering & Agile Principles (BTech CSE)
        // Questions: 12 MCQs (minimum 10 required)
        // =========================================================================
        $exam8 = Exam::updateOrCreate(
            [
                'title' => 'End-Semester Evaluation: Software Engineering & Agile Principles',
                'batch_id' => $batch1->id,
            ],
            [
                'subject' => 'CS301 - Software Engineering & System Architecture',
                'instructions' => "1. 12 questions on SDLC Models, Scrum Ceremonies, Design Patterns, and Testing.\n2. Duration: 35 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 35,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam8Questions = [
            [
                'question_text' => 'Which traditional software process model is sequential and best suited when requirements are strictly defined, fully understood, and changes are minimal?',
                'option_a' => 'Agile Scrum Model',
                'option_b' => 'Waterfall Model',
                'option_c' => 'Spiral Model',
                'option_d' => 'Evolutionary Prototyping',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'In Agile Scrum project management, what is a fixed, time-boxed iteration of work (typically lasting 1 to 4 weeks) called?',
                'option_a' => 'Sprint',
                'option_b' => 'Milestone',
                'option_c' => 'Epic',
                'option_d' => 'Release Cycle',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which software testing methodology focuses on testing code internal logic, branch coverage, and conditional paths by examining the source code?',
                'option_a' => 'Black-Box Testing',
                'option_b' => 'White-Box Testing (Structural Testing)',
                'option_c' => 'Acceptance Testing',
                'option_d' => 'Exploratory Testing',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which creational Gang of Four (GoF) design pattern ensures that a class has only one instance and provides a global access point to it?',
                'option_a' => 'Factory Method Pattern',
                'option_b' => 'Singleton Pattern',
                'option_c' => 'Builder Pattern',
                'option_d' => 'Prototype Pattern',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What document serves as the formal specification agreement between stakeholders, clients, and developers defining functional and non-functional requirements?',
                'option_a' => 'SRS (Software Requirements Specification)',
                'option_b' => 'SLA (Service Level Agreement)',
                'option_c' => 'API Reference Guide',
                'option_d' => 'UML Class Diagram',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'In software architecture design, which combination represents the ideal characteristic of high-quality modular software components?',
                'option_a' => 'High Coupling and Low Cohesion',
                'option_b' => 'Low Coupling and High Cohesion',
                'option_c' => 'High Coupling and High Cohesion',
                'option_d' => 'Zero Coupling and Zero Cohesion',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which software engineering metric measures the number of linearly independent paths through program source code to determine testing complexity?',
                'option_a' => 'Lines of Code (LOC)',
                'option_b' => 'Cyclomatic Complexity (McCabe metric)',
                'option_c' => 'Halstead Volume',
                'option_d' => 'Function Point (FP)',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What type of software testing is executed to confirm that recent code modifications, bug fixes, or enhancements have not broken previously functioning features?',
                'option_a' => 'Unit Testing',
                'option_b' => 'Stress Testing',
                'option_c' => 'Regression Testing',
                'option_d' => 'Smoke Testing',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which Scrum ceremony is held at the end of a sprint where the engineering team inspects past performance and identifies continuous process improvements?',
                'option_a' => 'Sprint Planning',
                'option_b' => 'Daily Standup',
                'option_c' => 'Sprint Review / Demo',
                'option_d' => 'Sprint Retrospective',
                'correct_option' => 'D',
            ],
            [
                'question_text' => 'Which Extreme Programming (XP) development practice pairs two programmers working simultaneously at a single workstation on the same code?',
                'option_a' => 'Test-Driven Development (TDD)',
                'option_b' => 'Pair Programming',
                'option_c' => 'Continuous Integration',
                'option_d' => 'Refactoring',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which behavioral design pattern defines a one-to-many relationship where state changes in a subject automatically notify and update dependent objects?',
                'option_a' => 'Adapter Pattern',
                'option_b' => 'Decorator Pattern',
                'option_c' => 'Observer Pattern',
                'option_d' => 'Strategy Pattern',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What DevOps engineering practice involves developers automatically integrating code changes into a central repository followed by automated test builds?',
                'option_a' => 'Continuous Integration (CI)',
                'option_b' => 'Agile Modeling',
                'option_c' => 'Scrum of Scrums',
                'option_d' => 'Waterfall Verification',
                'correct_option' => 'A',
            ],
        ];

        foreach ($exam8Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam8->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 9: Terminal Assessment: Web Technology & Full-Stack Development (BCA / Open)
        // Questions: 10 MCQs (minimum 10 required)
        // =========================================================================
        $exam9 = Exam::updateOrCreate(
            [
                'title' => 'Terminal Assessment: Web Technology & Full-Stack Development',
                'batch_id' => $batch3->id,
            ],
            [
                'subject' => 'CS208 - Modern Web Development & Full-Stack Architecture',
                'instructions' => "1. 10 MCQs on HTML5, CSS3, DOM manipulation, REST APIs, and ES6+ JavaScript.\n2. Duration: 35 minutes.\n3. Each question carries 2 marks.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 35,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam9Questions = [
            [
                'question_text' => 'Which HTML5 semantic element is designed specifically to contain footer information, authorship, and copyright notices?',
                'option_a' => '<bottom>',
                'option_b' => '<footer>',
                'option_c' => '<section-end>',
                'option_d' => '<aside>',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'In modern JavaScript (ES6+), which keyword is used to declare a block-scoped variable that cannot be re-assigned?',
                'option_a' => 'var',
                'option_b' => 'let',
                'option_c' => 'const',
                'option_d' => 'static',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which DOM method is the standard approach to bind an event listener to an HTML element without overwriting existing handlers?',
                'option_a' => 'element.attachEvent()',
                'option_b' => 'element.addEventListener()',
                'option_c' => 'element.bindEvent()',
                'option_d' => 'element.setEventHandler()',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What does CSS stand for in standard web development?',
                'option_a' => 'Computer Style Sheets',
                'option_b' => 'Creative Styling Syntax',
                'option_c' => 'Cascading Style Sheets',
                'option_d' => 'Colored Sheet Styles',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Which HTTP status code indicates a successful request in the standard HTTP protocol?',
                'option_a' => '200 OK',
                'option_b' => '301 Moved Permanently',
                'option_c' => '404 Not Found',
                'option_d' => '500 Internal Server Error',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which CSS layout model provides two-dimensional grid-based layout control over both columns and rows simultaneously?',
                'option_a' => 'CSS Flexbox',
                'option_b' => 'CSS Grid Layout',
                'option_c' => 'Float Positioning',
                'option_d' => 'Table Model',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which HTTP method in RESTful API design is conventionally used to completely replace an existing resource?',
                'option_a' => 'GET',
                'option_b' => 'POST',
                'option_c' => 'PUT',
                'option_d' => 'PATCH',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What data interchange format uses human-readable key-value pairs and is the standard for modern REST APIs?',
                'option_a' => 'XML',
                'option_b' => 'JSON (JavaScript Object Notation)',
                'option_c' => 'YAML',
                'option_d' => 'CSV',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which browser Web Storage mechanism retains stored data across browser restarts with no expiration date until explicitly cleared?',
                'option_a' => 'sessionStorage',
                'option_b' => 'localStorage',
                'option_c' => 'HTTP Cookies',
                'option_d' => 'MemoryCache',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which browser security policy restricts how a script loaded from one origin can interact with resources from a different origin?',
                'option_a' => 'Cross-Origin Resource Sharing (CORS) / Same-Origin Policy',
                'option_b' => 'Content Security Encryption',
                'option_c' => 'TLS Handshake Rule',
                'option_d' => 'Subresource Integrity Check',
                'correct_option' => 'A',
            ],
        ];

        foreach ($exam9Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam9->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // =========================================================================
        // Test 10: Foundational Evaluation: Digital Electronics & Logic Design (BCA / Open)
        // Questions: 10 MCQs (minimum 10 required)
        // =========================================================================
        $exam10 = Exam::updateOrCreate(
            [
                'title' => 'Diagnostic Test: Digital Electronics & Logic Design',
                'batch_id' => $batch4->id,
            ],
            [
                'subject' => 'CS105 - Digital Logic Design & Computer Fundamentals',
                'instructions' => "1. Foundational test covering Boolean algebra, logic gates, and processor units.\n2. 10 questions, 2 marks each.\n3. Save each answer and click submit when done.",
                'exam_date' => $today,
                'start_time' => '00:00:00',
                'duration_minutes' => 30,
                'marks_per_question' => 2,
                'status' => 'published',
            ]
        );

        $exam10Questions = [
            [
                'question_text' => 'Which fundamental logic gate produces an output of HIGH (1) if and only if all of its inputs are HIGH (1)?',
                'option_a' => 'OR Gate',
                'option_b' => 'AND Gate',
                'option_c' => 'XOR Gate',
                'option_d' => 'NAND Gate',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'What is the primary function of the Arithmetic Logic Unit (ALU) inside a central processing unit (CPU)?',
                'option_a' => 'Storing permanent operating system files',
                'option_b' => 'Performing arithmetic calculations and logical decision operations',
                'option_c' => 'Managing peripheral display devices',
                'option_d' => 'Generating clock pulse frequencies',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which type of computer memory is volatile, meaning its data is lost when electrical power is switched off?',
                'option_a' => 'ROM (Read Only Memory)',
                'option_b' => 'Flash Memory',
                'option_c' => 'RAM (Random Access Memory)',
                'option_d' => 'Hard Disk Drive',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'In a microprocessor memory hierarchy, which memory component provides the fastest read/write access speed to the CPU core?',
                'option_a' => 'L1 Cache',
                'option_b' => 'L3 Cache',
                'option_c' => 'Main RAM',
                'option_d' => 'Solid State Drive (SSD)',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'What is the 8-bit binary representation of the decimal integer value 10?',
                'option_a' => '00001010',
                'option_b' => '00001100',
                'option_c' => '00001001',
                'option_d' => '00000101',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Which of the following logic gates is designated as a "Universal Logic Gate" because any Boolean logic circuit can be built exclusively with it?',
                'option_a' => 'AND Gate',
                'option_b' => 'OR Gate',
                'option_c' => 'NAND Gate',
                'option_d' => 'XOR Gate',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'According to De Morgan\'s Theorem in Boolean algebra, what is the complement of (A · B)?',
                'option_a' => 'A\' · B\'',
                'option_b' => 'A\' + B\'',
                'option_c' => 'A + B',
                'option_d' => '(A + B)\'',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'How many select lines are required for a 8-to-1 Multiplexer (MUX)?',
                'option_a' => '2',
                'option_b' => '3',
                'option_c' => '4',
                'option_d' => '8',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Which sequential logic circuit component is capable of storing a single bit of binary state information?',
                'option_a' => 'Multiplexer',
                'option_b' => 'Decoder',
                'option_c' => 'Flip-Flop',
                'option_d' => 'Half Adder',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'What is the output sum (S) and carry (C) when adding two binary bits 1 and 1 using a Half Adder?',
                'option_a' => 'Sum = 0, Carry = 1',
                'option_b' => 'Sum = 1, Carry = 0',
                'option_c' => 'Sum = 1, Carry = 1',
                'option_d' => 'Sum = 0, Carry = 0',
                'correct_option' => 'A',
            ],
        ];

        foreach ($exam10Questions as $q) {
            Question::updateOrCreate(
                [
                    'exam_id' => $exam10->id,
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }

        // 5. Seeded completed examination attempt for Bipasha Sarkar on Exam 1 for instant reporting demonstration
        $bipashaQuestions = $exam1->questions()->orderBy('id')->get();
        if ($bipashaQuestions->count() >= 12) {
            $attempt = ExamAttempt::updateOrCreate(
                [
                    'exam_id' => $exam1->id,
                    'student_id' => $student2->id,
                ],
                [
                    'started_at' => now()->subMinutes(25),
                    'submitted_at' => now()->subMinutes(5),
                    'status' => 'submitted',
                    'total_questions' => 12,
                    'answered_count' => 12,
                    'unanswered_count' => 0,
                    'correct_count' => 10,
                    'incorrect_count' => 2,
                    'score' => 20.00,
                ]
            );

            // Seed student answers for Bipasha
            foreach ($bipashaQuestions as $idx => $q) {
                // Give correct answer for most, 2 wrong answers for realistic report
                $chosen = ($idx === 4 || $idx === 9) ? ($q->correct_option === 'A' ? 'B' : 'A') : $q->correct_option;
                $isCorrect = ($chosen === $q->correct_option);

                StudentAnswer::updateOrCreate(
                    [
                        'exam_attempt_id' => $attempt->id,
                        'question_id' => $q->id,
                    ],
                    [
                        'selected_option' => $chosen,
                        'is_correct' => $isCorrect,
                        'marks_awarded' => $isCorrect ? $exam1->marks_per_question : 0,
                    ]
                );
            }
        }
    }
}

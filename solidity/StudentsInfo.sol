pragma solidity ^0.4.21;

contract StudentsInfo {
    struct Grade {
        string class;
        uint8 grade;
    }

    struct Student {
        address addr;
        string names;
        uint8 number;
        mapping(uint => Grade) grades;
        uint gradesLength;
    }


    Student[] private students;

    mapping(uint8 => uint) private indexOfStudentByNumber;

    address private teacher;

    constructor() public{
        teacher = msg.sender;
    }

    modifier onlyTeacher(){
        require(msg.sender == teacher);
        _;
    }

    function createStudent(string names, uint8 number, address addr) public onlyTeacher {
        require(indexOfStudentByNumber[number] == 0, "Error");

        Student memory student = Student({
            names : names,
            addr : addr,
            number : number,
            gradesLength : 0
            });

        students.push(student);

        indexOfStudentByNumber[number] = students.length - 1;
    }

    function logGrade(uint8 number, string class, uint8 grade) public onlyTeacher {
        uint gradesIndex = students[indexOfStudentByNumber[number]].gradesLength;
        students[indexOfStudentByNumber[number]].gradesLength++;

        students[indexOfStudentByNumber[number]].grades[gradesIndex] = Grade({
            class : class,
            grade : grade
            });
    }

    function getStudentInfo(uint index) view public returns (string name, address addr, uint8 number, uint gradesCount){
        require(students.length > index);

        return (students[index].names, students[index].addr, students[index].number, students[index].gradesLength);
    }

    function getStudentsGrade(uint studentIndex, uint gradeIndex) view public returns (string class, uint8 grade){
        require(students.length > studentIndex);
        require(students[studentIndex].gradesLength > gradeIndex);

        return (students[studentIndex].grades[gradeIndex].class, students[studentIndex].grades[gradeIndex].grade);

    }
}

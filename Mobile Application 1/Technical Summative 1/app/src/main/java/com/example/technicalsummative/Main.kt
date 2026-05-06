package com.example.technicalsummative

fun main() {
    println("========ENTER STUDENT INFORMATION========")
    print("ID Number: ")
    val idNumber = readln()

    print("Full Name: ")
    val fullName = readln()

    print("Course: ")
    val course = readln()

    print("Year Level: ")
    val yearLevel = readln().toInt()

    val student = Student(idNumber, fullName, course, yearLevel)

    println("\n==========Student Information==========")
    student.displayInfo()
}
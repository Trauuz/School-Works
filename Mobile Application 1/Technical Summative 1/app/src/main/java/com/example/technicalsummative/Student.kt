package com.example.technicalsummative

class Student(
    idNumber: String,
    fullName: String,
    val course: String,
    val yearLevel: Int
) : Person(idNumber, fullName) {

    constructor(idNumber: String, fullName: String) : this(
        idNumber,
        fullName,
        course = "N/a",
        yearLevel = 0
    )

    override fun displayInfo() {
        println("ID Number: $idNumber")
        println("Full Name: $fullName")
        println("Course: $course")
        println("Year Level: $yearLevel")
    }
}
package com.example.technicalsummative

open class Person(val idNumber: String, val fullName: String) {
    open fun displayInfo() {
        println("ID Number: $idNumber")
        println("Full Name: $fullName")
    }
}
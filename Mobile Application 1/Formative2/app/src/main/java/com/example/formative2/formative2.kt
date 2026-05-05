package com.example.formative2

import java.time.Year

open class Person(
    private var firstName: String = "",
    private var middleName: String = "",
    private var lastName: String = "",
    private var birthday: String = "",
    private var gender: String = "",
    private var address: String = ""
) {

    fun setFirstName(firstName: String) { this.firstName = firstName }
    fun setMiddleName(middleName: String) { this.middleName = middleName }
    fun setLastName(lastName: String) { this.lastName = lastName }
    fun setBirthday(birthday: String) { this.birthday = birthday }
    fun setGender(gender: String) { this.gender = gender }
    fun setAddress(address: String) { this.address = address }


    fun getFirstName(): String = firstName
    fun getMiddleName(): String = middleName
    fun getLastName(): String = lastName
    fun getBirthday(): String = birthday
    fun getGender(): String = gender
    fun getAddress(): String = address


    fun calculateAge(): Int {
        val birthYear = birthday.substring(birthday.length - 4).toInt()
        val currentYear = Year.now().value
        return currentYear - birthYear
    }

    fun getFullName(): String = "$firstName $middleName $lastName"
    fun getBirthYear(): Int = birthday.substring(birthday.length - 4).toInt()
}


class Employee : Person() {
    private var tin: String = ""
    private var sssNumber: String = ""
    private var philHealthNumber: String = ""
    private var schoolGraduatedFrom: String = ""
    private var yearGraduated: String = ""
    private var skills: Array<String> = arrayOf("", "", "")
    private var personalReferences: Array<String> = arrayOf("", "", "")
    private var referenceContacts: Array<String> = arrayOf("", "", "")


    fun setTIN(tin: String) { this.tin = tin }
    fun setSSSNumber(sssNumber: String) { this.sssNumber = sssNumber }
    fun setPhilHealthNumber(philHealthNumber: String) { this.philHealthNumber = philHealthNumber }
    fun setSchoolGraduatedFrom(school: String) { this.schoolGraduatedFrom = school }
    fun setYearGraduated(year: String) { this.yearGraduated = year }

    fun setSkill(index: Int, skill: String) {
        if (index in 0..2) skills[index] = skill
    }

    fun setPersonalReference(index: Int, name: String, contact: String) {
        if (index in 0..2) {
            personalReferences[index] = name
            referenceContacts[index] = contact
        }
    }

    fun getTIN(): String = tin
    fun getSSSNumber(): String = sssNumber
    fun getPhilHealthNumber(): String = philHealthNumber
    fun getSchoolGraduatedFrom(): String = schoolGraduatedFrom
    fun getYearGraduated(): String = yearGraduated
    fun getSkills(): Array<String> = skills
    fun getPersonalReferences(): Array<String> = personalReferences
    fun getReferenceContacts(): Array<String> = referenceContacts

    fun displayInfo() {
        println("\n")
        println("                              EMPLOYEE BIO-DATA                               ")
        println("  Full Name     : ${getFullName()}")
        println("  Birthday      : ${getBirthday()}")
        println("  Birth Year    : ${getBirthYear()}")
        println("  Age           : ${calculateAge()} years old")
        println("  Gender        : ${getGender()}")
        println("  Address       : ${getAddress()}")
        println("  TIN Number    : $tin")
        println("  SSS Number    : $sssNumber")
        println("  PhilHealth    : $philHealthNumber")
        println("  College       : $schoolGraduatedFrom")
        println("  Year Graduated: $yearGraduated")

        println("\n  Knowledge/Skills:")
        for(i in 0..2) {
            if(skills[i].isNotEmpty()) {
                println("    • ${skills[i]}")
            }
        }

        println("\n  Personal References:")
        for(i in 0..2) {
            if(personalReferences[i].isNotEmpty()) {
                println("    ${i + 1}. ${personalReferences[i]}")
                println("       Contact: ${referenceContacts[i]}")
            }
        }
    }
}

class Student : Person() {
    private var degreeProgram: String = ""
    private var yearLevel: String = ""
    private var specialization: String = ""
    private var section: String = ""

    // Setters
    fun setDegreeProgram(degreeProgram: String) { this.degreeProgram = degreeProgram }
    fun setYearLevel(yearLevel: String) { this.yearLevel = yearLevel }
    fun setSpecialization(specialization: String) { this.specialization = specialization }
    fun setSection(section: String) { this.section = section }

    // Getters
    fun getDegreeProgram(): String = degreeProgram
    fun getYearLevel(): String = yearLevel
    fun getSpecialization(): String = specialization
    fun getSection(): String = section

    // Display all student information
    fun displayInfo() {
        println("\n")
        println("                              STUDENT BIO-DATA                               ")
        println("  Full Name     : ${getFullName()}")
        println("  Birthday      : ${getBirthday()}")
        println("  Birth Year    : ${getBirthYear()}")
        println("  Age           : ${calculateAge()} years old")
        println("  Gender        : ${getGender()}")
        println("  Address       : ${getAddress()}")
        println("  Degree Program: $degreeProgram")
        println("  Year Level    : $yearLevel")
        println("  Specialization: $specialization")
        println("  Section       : $section")
    }
}

fun main() {
    var choice: Int

    do {
        println("\n" + "=".repeat(50))
        println("              INFORMATION INPUT MENU")
        println("=".repeat(50))
        println("1. Input Employee Information")
        println("2. Input Student Information")
        println("3. Exit")
        print("\nEnter your choice: ")
        choice = readln().toIntOrNull() ?: 0

        when(choice) {
            1 -> inputEmployeeInfo()
            2 -> inputStudentInfo()
            3 -> println("Exiting program. Goodbye!")
            else -> println("Invalid choice! Please enter 1, 2, or 3.")
        }
    } while(choice != 3)
}

fun inputEmployeeInfo() {
    val employee = Employee()

    println("\n--- EMPLOYEE INFORMATION INPUT ---")
    print("Enter First Name: ")
    employee.setFirstName(readln())

    print("Enter Middle Name: ")
    employee.setMiddleName(readln())

    print("Enter Last Name: ")
    employee.setLastName(readln())

    print("Enter Birthday (MM/DD/YYYY): ")
    employee.setBirthday(readln())

    print("Enter Gender: ")
    employee.setGender(readln())

    print("Enter Address: ")
    employee.setAddress(readln())

    print("Enter TIN (or N/A): ")
    employee.setTIN(readln())

    print("Enter SSS number (or N/A): ")
    employee.setSSSNumber(readln())

    print("Enter PhilHealth (or N/A): ")
    employee.setPhilHealthNumber(readln())

    print("Enter School Graduated From: ")
    employee.setSchoolGraduatedFrom(readln())

    print("Enter Graduated Year: ")
    employee.setYearGraduated(readln())

    println("\n--- Enter Your Skills (at most 3) ---")
    for(i in 0..2) {
        print("Enter Skill ${i + 1}: ")
        employee.setSkill(i, readln())
    }

    println("\n--- Enter Personal References (at least 3) ---")
    for(i in 0..2) {
        println("\nPersonal Reference ${i + 1}:")
        print("  Name: ")
        val name = readln()
        print("  Contact Number: ")
        val contact = readln()
        employee.setPersonalReference(i, name, contact)
    }

    employee.displayInfo()
}

fun inputStudentInfo() {
    val student = Student()

    println("\n--- STUDENT INFORMATION INPUT ---")
    print("Enter First Name: ")
    student.setFirstName(readln())

    print("Enter Middle Name: ")
    student.setMiddleName(readln())

    print("Enter Last Name: ")
    student.setLastName(readln())

    print("Enter Birthday (MM/DD/YYYY): ")
    student.setBirthday(readln())

    print("Enter Gender: ")
    student.setGender(readln())

    print("Enter Address: ")
    student.setAddress(readln())

    print("Enter Degree Program: ")
    student.setDegreeProgram(readln())

    print("Enter Year Level: ")
    student.setYearLevel(readln())

    print("Enter Specialization (or N/A): ")
    student.setSpecialization(readln())

    print("Enter Section: ")
    student.setSection(readln())

    student.displayInfo()
}
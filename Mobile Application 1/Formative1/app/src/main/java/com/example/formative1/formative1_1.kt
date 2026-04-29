package com.example.formative1

fun main() {
    print("Enter First Name: ")
    val firstName = readln()

    print("Enter Middle Name: ")
    val middleName = readln()

    print("Enter Last Name: ")
    val lastName = readln()

    print("Enter Birthday (MM/DD/YYYY): ")
    val birthday = readln()

    val birthYear = birthday.substring(birthday.length - 4).toInt()
    val age = 2026 - birthYear

    print("Enter Gender: ")
    val gender = readln()

    print("Enter Address: ")
    val address = readln()

    print("Enter TIN (or N/A): ")
    val tin = readln()

    print("Enter SSS number (or N/A): ")
    val sssNumber = readln()

    print("Enter PhilHealth (or N/A): ")
    val philHealth = readln()

    print("Enter School Graduated From: ")
    val collegeGraduate = readln()

    print("Enter Graduated Year: ")
    val graduateDate = readln()

    val skills = arrayOf("", "", "")
    println("\n--- Enter Your Skills ---")
    for(i in 0..2){
        print("Enter Skill ${i + 1}: ")
        skills[i] = readln()
    }

    val personalReferences = arrayOf("", "", "")
    val contactNumbers = arrayOf("", "", "")
    println("\n--- Enter Personal References  ---")
    for(i in 0..2){
        println("\nPersonal Reference ${i + 1}:")
        print("  Name: ")
        personalReferences[i] = readln()
        print("  Contact Number: ")
        contactNumbers[i] = readln()
    }

    displayBiodata(
        firstName, middleName, lastName, age, birthYear, gender, address,
        tin, sssNumber, philHealth, collegeGraduate, graduateDate,
        skills, personalReferences, contactNumbers
    )
}

fun displayBiodata(
    firstName: String, middleName: String, lastName: String, age: Int, birthYear: Int, gender: String, address: String,
    tin: String, sssNumber: String, philHealth: String, collegeGraduate: String, graduateDate: String,
    skills: Array<String>, personalReferences: Array<String>, contactNumbers: Array<String>
) {
    println("\n")
    println("                              PERSONAL BIO-DATA                               ")
    println("  Full Name     : $firstName $middleName $lastName")
    println("  Birth Year    : $birthYear")
    println("  Age           : $age years old")
    println("  Gender        : $gender")
    println("  Address       : $address")
    println("  TIN Number    : $tin")
    println("  SSS Number    : $sssNumber")
    println("  PhilHealth    : $philHealth")
    println("  College       : $collegeGraduate")
    println("  Year Graduated: $graduateDate")
    for(i in 0..2) {
        if(skills[i].isNotEmpty()) {
            println("  • ${skills[i]}")
        }
    }

    for(i in 0..2) {
        if(personalReferences[i].isNotEmpty()) {
            println("  ${i + 1}. ${personalReferences[i]}")
            println("  Contact: ${contactNumbers[i]}")
        }
    }
}
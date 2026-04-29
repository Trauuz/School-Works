package com.example.formative1

class Volume {
    val sphereVolume: (Double) -> Double = { radius ->
        (4.0 / 3.0) * 3.14159 * radius * radius * radius
    }

    val pyramidVolume: (Double, Double) -> Double = { baseSide, height ->
        (1.0 / 3.0) * (baseSide * baseSide) * height
    }

    val rectangularPrismVolume: (Double, Double, Double) -> Double = { length, width, height ->
        length * width * height
    }

    val cubeVolume: (Double) -> Double = { side ->
        side * side * side
    }

    fun compute(vararg values: Double, operation: (DoubleArray) -> Double): Double {
        return operation(values)
    }
}

fun main() {
    val volume = Volume()

    println("======================================")
    println(" VOLUME CALCULATOR")
    println("======================================")
    println("1. Sphere")
    println("2. Pyramid")
    println("3. Rectangular Prism")
    println("4. Cube")
    print("\nChoose (1-4): ")

    val choice = readln().toInt()

    when (choice) {
        1 -> {
            print("Enter radius: ")
            val radius = readln().toDouble()

            val result = volume.compute(radius) { values ->
                volume.sphereVolume(values[0])
            }

            println("======================================")
            println("Sphere Volume = $result")
            println("======================================")
        }

        2 -> {
            print("Enter base side: ")
            val baseSide = readln().toDouble()
            print("Enter height: ")
            val height = readln().toDouble()

            val result = volume.compute(baseSide, height) { values ->
                volume.pyramidVolume(values[0], values[1])
            }

            println("======================================")
            println("Pyramid Volume = $result")
            println("======================================")
        }

        3 -> {
            print("Enter length: ")
            val length = readln().toDouble()
            print("Enter width: ")
            val width = readln().toDouble()
            print("Enter height: ")
            val height = readln().toDouble()

            val result = volume.compute(length, width, height) { values ->
                volume.rectangularPrismVolume(values[0], values[1], values[2])
            }

            println("======================================")
            println("Rectangular Prism Volume = $result")
            println("======================================")
        }

        4 -> {
            print("Enter side: ")
            val side = readln().toDouble()

            val result = volume.compute(side) { values ->
                volume.cubeVolume(values[0])
            }

            println("======================================")
            println("Cube Volume = $result")
            println("======================================")
        }

        else -> {
            println("Invalid choice!")
        }
    }
}
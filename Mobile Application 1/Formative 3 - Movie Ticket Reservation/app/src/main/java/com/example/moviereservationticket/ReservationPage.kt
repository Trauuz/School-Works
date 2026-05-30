package com.example.moviereservationticket

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class ReservationPage : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_reservation_page)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        val etMovieTitle = findViewById<EditText>(R.id.etMovieTitle)
        val etCustomerName = findViewById<EditText>(R.id.etCustomerName)
        val etTicketCount = findViewById<EditText>(R.id.etTicketCount)
        val btnReserve = findViewById<Button>(R.id.btnReserve)
        val tvSummary = findViewById<TextView>(R.id.tvSummary)

        // Set initial summary text to empty or a placeholder
        tvSummary.text = ""

        btnReserve.setOnClickListener {
            val movieTitle = etMovieTitle.text.toString().trim()
            val customerName = etCustomerName.text.toString().trim()
            val ticketCount = etTicketCount.text.toString().trim()

            if (movieTitle.isEmpty() || customerName.isEmpty() || ticketCount.isEmpty()) {
                tvSummary.text = "Please fill in all fields."
            } else {
                val summary = "Reservation Summary:\n" +
                        "Movie: $movieTitle\n" +
                        "Customer: $customerName\n" +
                        "Tickets: $ticketCount"
                tvSummary.text = summary
            }
        }
    }
}
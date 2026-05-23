package com.example.androidapplication

import android.os.Bundle
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import com.example.androidapplication.databinding.ActivitySignUpBinding

class SignUp : AppCompatActivity() {
    // 1. declaring a binding object
    private lateinit var binding: ActivitySignUpBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        // 2. inflate the binding object using layoutInflater
        binding = ActivitySignUpBinding.inflate(layoutInflater)

        // 3. set the binding object as the content view
        setContentView(binding.root)

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        // variable declarations to accept contents of the sign up from
        val firstName = binding.etSignupFirstName.text.toString()
        val lastName = binding.etSignupLastName.text.toString()
        val email = binding.etSignupEmail.text.toString()
        val password = binding.etSignupPassword.text.toString()
        val confirmPassword = binding.etSignupConfirmPassword.text.toString()

        if (password.equals(confirmPassword)) {
            Toast.makeText(this, "Password does not match...", Toast.LENGTH_LONG).show()
        } else {
            Toast.makeText(this, "Passwords does not match...", Toast.LENGTH_LONG).show()
        }
    }
}
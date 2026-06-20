package com.example.campusconnect

import android.Manifest
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.pm.PackageManager
import android.os.Build
import android.os.Bundle
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import com.example.campusconnect.databinding.ActivityMainBinding
import android.view.Menu
import android.view.MenuItem
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat

class MainActivity : AppCompatActivity() {
    // Create biding object
    private lateinit var binding: ActivityMainBinding

    private val channelId = "channel"
    private var notificationId = 1
    private val requestPermissionLauncher =
        registerForActivityResult(
            ActivityResultContracts.RequestPermission()
        ) {
            granted -> if(granted) sendNotification()
            else Toast.makeText(this, "Notification is not granted", Toast.LENGTH_LONG
            )
        }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        // Initialize the binding object
        binding = ActivityMainBinding.inflate(layoutInflater)

        enableEdgeToEdge()

        //Set the binding object as the layout
        setContentView(binding.root)

        //Set the toolbar as the action bar
        setSupportActionBar(binding.toolbar)
        supportActionBar?.title = "Campus Connect"

        binding.bottomNavigation.setOnItemSelectedListener { item ->
            val fragment = when (item.itemId) {
                R.id.bn_home -> HomeFragment()
                R.id.bn_dashboard -> DashboardFragment()
                R.id.bn_notifications -> NotificationFragment()
                else -> HomeFragment()
            }
            supportFragmentManager.beginTransaction().replace(R.id.fragment_container, fragment)
                .commit()

            true
        }

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main)) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom)
            insets
        }

        if (savedInstanceState == null) {
            binding.bottomNavigation.selectedItemId = R.id.bn_home
        }

    }

    //Inflate the toolbar with the main menu
    override fun onCreateOptionsMenu(menu: Menu): Boolean {
        //Use the menu inflater to inflate the menu
        menuInflater.inflate(R.menu.main_menu, menu)
        return true
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean =
        when(item.itemId){
            R.id.mn_about -> {
                Toast.makeText(this, "About menu is selected", Toast.LENGTH_LONG).show()
                true
            }
            R.id.mn_notify -> {
                checkPermissionAndNotify()
                true
            }
            R.id.mn_settings -> {
                Toast.makeText(this, "Settings menu is selected", Toast.LENGTH_LONG).show()
                true
            }
            else -> super.onOptionsItemSelected(item)
        }



    private fun createNotifcationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Campus Connect",
                NotificationManager.IMPORTANCE_DEFAULT
            ).apply {
                description = "pstttt... asa listahan ka raw ni dean! LETS GO!!!"
            }

            getSystemService(NotificationManager::class.java).createNotificationChannel(channel)
        }
    }

    private fun checkPermissionAndNotify() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU && ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.POST_NOTIFICATIONS
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            requestPermissionLauncher.launch(
                Manifest.permission.POST_NOTIFICATIONS
            )
        } else {
            sendNotification()
        }
    }

    private fun sendNotification(){
        val notification = NotificationCompat.Builder(this, channelId)
            .setSmallIcon(R.drawable.ic_notifications).setContentTitle("Campus Connect")
            .setContentText("pstttt... asa listahan ka raw ni dean! LETS GO!!!")
            .setPriority(NotificationCompat.PRIORITY_DEFAULT)
            .setAutoCancel(true)
            .build()

        getSystemService(NotificationManager::class.java).notify(notificationId++, notification)
    }
}
package com.example.control_inventario

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.viewModels
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.tooling.preview.Preview
import com.example.control_inventario.ui.theme.Control_inventarioTheme
import com.example.control_inventario.ui.screens.PantallaInventario
import com.example.control_inventario.ui.viewmodel.InventarioViewModel

class MainActivity : ComponentActivity() {
    // Obtenemos el ViewModel
    private val vm: InventarioViewModel by viewModels()
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        setContent {
            // Asumiendo que tienes un tema definido, si no, usa MaterialTheme
            MaterialTheme {
                Surface {
                    PantallaInventario(vm)
                }
            }
        }
    }
}


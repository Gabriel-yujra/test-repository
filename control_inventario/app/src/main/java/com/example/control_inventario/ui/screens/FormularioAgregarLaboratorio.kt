package com.example.control_inventario.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.control_inventario.ui.viewmodel.InventarioViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun FormularioAgregarLaboratorio(
    vm: InventarioViewModel,
    onLabAgregado: (String) -> Unit // Callback para mostrar mensaje
) {
    var nombre by remember { mutableStateOf("") }
    var ubicacion by remember { mutableStateOf("") }

    Card(modifier = Modifier.fillMaxWidth(), elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text("Registrar Nuevo Laboratorio", style = MaterialTheme.typography.titleMedium)

            OutlinedTextField(
                value = nombre,
                onValueChange = { nombre = it },
                label = { Text("Nombre del Laboratorio (Único)") },
                modifier = Modifier.fillMaxWidth(),
                singleLine = true
            )

            OutlinedTextField(
                value = ubicacion,
                onValueChange = { ubicacion = it },
                label = { Text("Ubicación (Ej: Piso 3, Monoblock)") },
                modifier = Modifier.fillMaxWidth(),
                singleLine = true
            )

            Button(
                onClick = {
                    if (nombre.isNotBlank() && ubicacion.isNotBlank()) {
                        vm.agregarLaboratorio(nombre, ubicacion)
                        onLabAgregado(nombre) // Llama al callback
                        nombre = "" // Limpiar campos
                        ubicacion = ""
                    }
                },
                modifier = Modifier.align(Alignment.End)
            ) {
                Text("Guardar Laboratorio")
            }
        }
    }
}
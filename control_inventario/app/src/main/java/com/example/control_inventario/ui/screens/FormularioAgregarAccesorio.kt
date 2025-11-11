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
fun FormularioAgregarAccesorio(
    vm: InventarioViewModel,
    listaCodigosPcs: List<String>, // <-- Recibimos la lista de PCs
    onAccesorioAgregado: (String) -> Unit
) {
    // --- Estados para el Dropdown ---
    var expandido by remember { mutableStateOf(false) }
    // Seleccionamos la primera PC por defecto, si existe
    var pcSeleccionada by remember(listaCodigosPcs) {
        mutableStateOf(listaCodigosPcs.firstOrNull() ?: "")
    }

    // --- Estados para los campos de texto ---
    var tipo by remember { mutableStateOf("") }
    var descripcion by remember { mutableStateOf("") }
    var serial by remember { mutableStateOf("") }

    Card(modifier = Modifier.fillMaxWidth(), elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text("Registrar Nuevo Accesorio", style = MaterialTheme.typography.titleMedium)

            // --- Dropdown de Computadoras ---
            ExposedDropdownMenuBox(
                expanded = expandido,
                onExpandedChange = { expandido = !expandido }
            ) {
                OutlinedTextField(
                    value = pcSeleccionada,
                    onValueChange = {},
                    readOnly = true,
                    label = { Text("Asignar a Computadora (Código)") },
                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expandido) },
                    modifier = Modifier
                        .fillMaxWidth()
                        .menuAnchor()
                )
                ExposedDropdownMenu(
                    expanded = expandido,
                    onDismissRequest = { expandido = false }
                ) {
                    listaCodigosPcs.forEach { codigoPc ->
                        DropdownMenuItem(
                            text = { Text(codigoPc) },
                            onClick = {
                                pcSeleccionada = codigoPc
                                expandido = false
                            }
                        )
                    }
                }
            }

            // --- Otros campos de texto ---
            OutlinedTextField(
                value = tipo,
                onValueChange = { tipo = it },
                label = { Text("Tipo (Ej: Mouse, Teclado, Monitor)") },
                modifier = Modifier.fillMaxWidth()
            )

            OutlinedTextField(
                value = descripcion,
                onValueChange = { descripcion = it },
                label = { Text("Descripción (Ej: Logitech G203, Samsung 24\")") },
                modifier = Modifier.fillMaxWidth()
            )

            OutlinedTextField(
                value = serial,
                onValueChange = { serial = it },
                label = { Text("Serial (Opcional)") },
                modifier = Modifier.fillMaxWidth()
            )

            // --- Botón de Guardar ---
            Button(
                onClick = {
                    if (pcSeleccionada.isNotBlank() && tipo.isNotBlank() && descripcion.isNotBlank()) {
                        vm.agregarAccesorio(
                            computadoraCodigo = pcSeleccionada,
                            tipo = tipo,
                            descripcion = descripcion,
                            serial = serial.ifBlank { null } // Envía null si está vacío
                        )
                        onAccesorioAgregado(tipo)
                        // Limpiar campos
                        tipo = ""; descripcion = ""; serial = ""
                        // No limpiamos pcSeleccionada, por si el usuario
                        // quiere agregar varios accesorios a la misma PC
                    }
                },
                modifier = Modifier.align(Alignment.End),
                // Deshabilitado si no hay PC seleccionada o campos obligatorios vacíos
                enabled = pcSeleccionada.isNotBlank() && tipo.isNotBlank() && descripcion.isNotBlank()
            ) {
                Text("Guardar Accesorio")
            }
        }
    }
}
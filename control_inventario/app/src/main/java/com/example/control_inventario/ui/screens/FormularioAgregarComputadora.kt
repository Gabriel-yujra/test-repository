package com.example.control_inventario.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import com.example.control_inventario.data.db.entity.Computadora
import com.example.control_inventario.ui.viewmodel.InventarioViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun FormularioAgregarComputadora(
    vm: InventarioViewModel,
    listaNombresLabs: List<String>, // La lista de nombres que pasamos
    pcParaEditar: Computadora?,
    onPcAgregada: (String) -> Unit
) {
    // --- Estados para el Dropdown ---
    var expandido by remember { mutableStateOf(false) }
    var labSeleccionado by remember { mutableStateOf(listaNombresLabs.firstOrNull() ?: "") }

    // --- Estados para los campos de texto ---
    var codigo by remember { mutableStateOf("") }
    var marca by remember { mutableStateOf("") }
    var modelo by remember { mutableStateOf("") }
    var cpu by remember { mutableStateOf("") }
    var ram by remember { mutableStateOf("") }
    var estado by remember { mutableStateOf("") }

    val enModoEdicion = pcParaEditar != null
    // Efecto para rellenar el formulario
    LaunchedEffect(pcParaEditar) {
        if (pcParaEditar != null) {
            // Modo Edición: Rellenar campos
            labSeleccionado = pcParaEditar.laboratorioNombre
            codigo = pcParaEditar.codigo
            marca = pcParaEditar.marca
            modelo = pcParaEditar.modelo
            cpu = pcParaEditar.cpu
            ram = pcParaEditar.ramGB.toString()
            estado = pcParaEditar.estado
        } else {
            // Modo Agregar: Limpiar campos (excepto el lab seleccionado)
            labSeleccionado = listaNombresLabs.firstOrNull() ?: ""
            codigo = ""
            marca = ""
            modelo = ""
            cpu = ""
            ram = ""
            estado = ""
        }
    }
    Card(modifier = Modifier.fillMaxWidth(), elevation = CardDefaults.cardElevation(2.dp)) {
        Column(Modifier.padding(16.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text(
                if (enModoEdicion) "Editar Computadora" else "Registrar Nueva Computadora",
                style = MaterialTheme.typography.titleMedium)

            // --- Dropdown de Laboratorios ---
            ExposedDropdownMenuBox(
                expanded = expandido,
                //onExpandedChange = { expandido = !expandido }
                onExpandedChange = { if (!enModoEdicion) expandido = !expandido } // No expandir si edita
            ) {
                OutlinedTextField(
                    value = labSeleccionado,
                    onValueChange = {},
                    readOnly = true,
                    label = { Text("Laboratorio") },
                    trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded = expandido) },
                    modifier = Modifier
                        .fillMaxWidth()
                        .menuAnchor(),
                    enabled = !enModoEdicion // Deshabilitado en edicion
                )
                ExposedDropdownMenu(
                    expanded = expandido,
                    onDismissRequest = { expandido = false }
                ) {
                    listaNombresLabs.forEach { nombreLab ->
                        DropdownMenuItem(
                            text = { Text(nombreLab) },
                            onClick = {
                                labSeleccionado = nombreLab
                                expandido = false
                            }
                        )
                    }
                }
            }


            // --- Otros campos de texto ---
            OutlinedTextField(
                value = codigo,
                onValueChange = { codigo = it },
                label = { Text("Código de PC (Único)") },
                modifier = Modifier.fillMaxWidth(),
                enabled = !enModoEdicion
            )

            // (Puedes agruparlos en un Row si quieres)
            OutlinedTextField(value = marca, onValueChange = { marca = it }, label = { Text("Marca") })
            OutlinedTextField(value = modelo, onValueChange = { modelo = it }, label = { Text("Modelo") })
            OutlinedTextField(value = cpu, onValueChange = { cpu = it }, label = { Text("CPU") })


            OutlinedTextField(
                value = ram,
                onValueChange = { if (it.all { c -> c.isDigit() }) ram = it }, // Validación
                label = { Text("RAM (GB)") },
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number) // Teclado numérico
            )

            OutlinedTextField(value = estado, onValueChange = { estado = it }, label = { Text("Estado (Operativa, Mantenimiento...)") })

            Row(
                Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.End,
                verticalAlignment = Alignment.CenterVertically
            ) {
                if (enModoEdicion) {
                    TextButton(onClick = { vm.cancelarEdicionPc() }) {
                        Text("Cancelar")
                    }
                    Spacer(Modifier.width(8.dp))
                }
            }
            // --- Botón de Guardar ---
            Button(
                onClick = {
                    val ramInt = ram.toIntOrNull()
                    if (codigo.isNotBlank() && labSeleccionado.isNotBlank() && marca.isNotBlank() && ramInt != null) {
                        vm.agregarComputadoras(
                            codigo = codigo,
                            laboratorioNombre = labSeleccionado,
                            marca = marca,
                            modelo = modelo,
                            cpu = cpu,
                            ramGB = ramInt,
                            estado = estado.ifBlank { "Operativa" } // Valor por defecto
                        )
                        onPcAgregada(codigo)
                        // Limpiar campos
                        //codigo = ""; marca = ""; modelo = ""; cpu = ""; ram = ""; estado = ""
                    }
                },
                modifier = Modifier.align(Alignment.End),
                // El botón está deshabilitado si el laboratorio no está seleccionado
                enabled = labSeleccionado.isNotBlank() && codigo.isNotBlank() && marca.isNotBlank()
            ) {
                Text(if (enModoEdicion) "Actualizar" else "Guardar Computadora")
            }
        }
    }
}
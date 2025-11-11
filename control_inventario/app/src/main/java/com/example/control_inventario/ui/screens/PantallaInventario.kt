package com.example.control_inventario.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.runtime.derivedStateOf
import androidx.compose.material.icons.filled.Edit
import androidx.compose.ui.Alignment
import com.example.control_inventario.data.db.relation.LaboratorioCompleto
import com.example.control_inventario.ui.viewmodel.InventarioViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PantallaInventario(vm: InventarioViewModel) {
    // Observamos el inventario completo
    val inventario by vm.inventarioCompleto.collectAsState()
    // Observar el estado de edicion
    val pcParaEditar by vm.pcEnEdicion.collectAsState()
    var mensaje by remember { mutableStateOf("") }
    // Obtenemos la lista de nombres de Laboratorios para el dropdown
    val nombresLabs by remember(inventario) {
        derivedStateOf { inventario.map { it.laboratorio.nombre } }
    }
    // --- NUEVO: Obtenemos la lista de TODOS los códigos de PC ---
    val listaCodigosPcs by remember(inventario) {
        derivedStateOf {
            inventario
                .flatMap { it.computadoras } // Obtenemos List<ComputadoraConAccesorios>
                .map { it.computadora.codigo } // Mapeamos a List<String>
        }
    }

    LazyColumn(
        Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        // TITULO Y MENSAJE
        item {
            Text("Inventario de Laboratorios UMSA", style = MaterialTheme.typography.headlineMedium)
            if (mensaje.isNotEmpty()){
                Text(mensaje, color = MaterialTheme.colorScheme.primary)
            }
        }
        // FORMULARIO DE LABORATORIO
        item {
            FormularioAgregarLaboratorio(
                vm = vm,
                onLabAgregado = { mensaje = "Laboratorio '$it' agregado." }
            )
        }
        // FORMULARIO DE COMPUTADORA
        item {
            // Solo mostramos este formulario si ya eiste al menos un laboratorio
            if (nombresLabs.isNotEmpty()) {
                FormularioAgregarComputadora(
                    vm = vm,
                    listaNombresLabs = nombresLabs,
                    pcParaEditar = pcParaEditar,
                    onPcAgregada = { mensaje = "Computadora '$it' guardada/actualizada." }
                )
            } else {
                Text("Agregue un laboratorio primero para poder registrar computadoras.")
            }
        }
        // --- NUEVO: FORMULARIO DE ACCESORIO ---
        item {
            // Solo mostramos si ya existe al menos una computadora
            if (listaCodigosPcs.isNotEmpty()) {
                FormularioAgregarAccesorio(
                    vm = vm,
                    listaCodigosPcs = listaCodigosPcs,
                    onAccesorioAgregado = { mensaje = "Accesorio '$it' agregado." }
                )
            } else {
                Text("Agregue una computadora primero para poder registrar accesorios.")
            }
        }
        // DIVISOR Y LISTA
        item { Divider(Modifier.padding(vertical = 8.dp)) }

        item {
            Text("Lista de Inventario", style = MaterialTheme.typography.titleMedium)
        }
        if (inventario.isEmpty()) {
            item { Text("No hay laboratorios registrados.") }
        }
        // LISTA DE INVENTARIO
        items(inventario, key = { it.laboratorio.nombre }) { labCompleto ->
            // El Card de la lista de inventario que definimos antes va aqui
            InventarioCard(labCompleto = labCompleto, vm = vm)
        }
    }
    /*
    // Aqui irian los OutlinerdTextField y Buttons para agregar Labs, PCs, etc.
    // Por simplicidad, esta UI solo muestra la lista.
    Column(Modifier.padding(16.dp)) {
        Text("Inventario de Laboratorios UMSA", style = MaterialTheme.typography.headlineMedium)
        Spacer(Modifier.height(16.dp))

        LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
            if (inventario.isEmpty()) {
                item { Text("No hay laboratorios registrados.") }
            }

            items(inventario, key = { it.laboratorio.nombre }) { labCompleto ->
                // tarjetas para cada Labortorio
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    elevation = CardDefaults.cardElevation(4.dp)
                ) {
                    Column(Modifier.padding(12.dp)) {
                        Text(
                            text = "Lab: ${labCompleto.laboratorio.nombre}",
                            style = MaterialTheme.typography.titleLarge
                        )
                        Text(
                            text = "Ubicación: ${labCompleto.laboratorio.ubicacion}",
                            style = MaterialTheme.typography.bodySmall
                        )
                        Spacer(Modifier.height(8.dp))
                        //Lista de Computadoras en este laboratorio
                        labCompleto.computadoras.forEach { pcConAcc ->
                            Column(Modifier.padding(start = 16.dp, top = 8.dp)) {
                                Row(
                                    Modifier.fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween
                                ) {
                                    Text(
                                        text = "PC: ${pcConAcc.computadora.codigo}",
                                        style = MaterialTheme.typography.titleMedium
                                    )
                                    IconButton(
                                        onClick = { vm.eliminarComputadora(pcConAcc.computadora.codigo)},
                                        modifier = Modifier.size(24.dp)
                                    ) {
                                        Icon(Icons.Default.Delete, "Eliminar PC",
                                            tint = MaterialTheme.colorScheme.error)
                                    }
                                }
                                Text(
                                    text = "${pcConAcc.computadora.marca} ${pcConAcc.computadora.modelo} (${pcConAcc.computadora.estado})",
                                    style = MaterialTheme.typography.bodyMedium
                                )
                                // Lista de Accesorios en esta computadora
                                pcConAcc.accesorios.forEach { acc ->
                                    Text(
                                        text = "• ${acc.tipo}: ${acc.descripcion}",
                                        style = MaterialTheme.typography.bodySmall,
                                        modifier = Modifier.padding(start = 16.dp)
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    */
}

@Composable
private fun InventarioCard(
    labCompleto: LaboratorioCompleto,
    vm: InventarioViewModel
) {
    // Tarjeta para cada Laboratorio
    Card(
        modifier = Modifier.fillMaxWidth(),
        elevation = CardDefaults.cardElevation(4.dp)
    ) {
        Column(Modifier.padding(12.dp)) {
            // Detalle del Laboratorio
            Text(
                text = "Lab: ${labCompleto.laboratorio.nombre}",
                style = MaterialTheme.typography.titleLarge
            )
            Text(
                text = "Ubicación: ${labCompleto.laboratorio.ubicacion}",
                style = MaterialTheme.typography.bodySmall
            )
            Spacer(Modifier.height(8.dp))
            // Lista de Computadoras en este Laboratorio
            if (labCompleto.computadoras.isEmpty()) {
                Text(
                    "No hay computadoras regostradas en este laboratorio.",
                    style = MaterialTheme.typography.bodySmall,
                    modifier = Modifier.padding(start = 16.dp, top = 8.dp)
                )
            } else {
                // Itera por cada computadora
                labCompleto.computadoras.forEach { pcConAcc ->
                    Column(Modifier.padding(start = 16.dp, top = 8.dp)) {
                        // Detalles de la Computadora
                        Row(
                            Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text(
                                text = "PC: ${pcConAcc.computadora.codigo}",
                                style = MaterialTheme.typography.titleMedium,
                                fontWeight = FontWeight.Bold
                            )
                            // Fila para los Iconos
                            Row {
                                IconButton(
                                    onClick = { vm.cargarPcParaEditar(pcConAcc.computadora) },
                                    modifier = Modifier.size(24.dp)
                                ) {
                                    Icon(
                                        Icons.Default.Edit,
                                        "Editar Pc",
                                        tint = MaterialTheme.colorScheme.primary
                                    )
                                }
                                Spacer(Modifier.width(8.dp))
                                IconButton(
                                    onClick = { vm.eliminarComputadora(pcConAcc.computadora.codigo) },
                                    modifier = Modifier.size(24.dp)
                                ) {
                                    Icon(Icons.Default.Delete,
                                        "Eliminar PC",
                                        tint = MaterialTheme.colorScheme.error)
                                }
                            }

                        }
                        Text(
                            text = "${pcConAcc.computadora.marca} ${pcConAcc.computadora.modelo} (${pcConAcc.computadora.estado})",
                            style = MaterialTheme.typography.bodyMedium
                        )
                        // Lista de Accesorios en esta Computadora
                        if (pcConAcc.accesorios.isEmpty()) {
                            Text(
                                text = "• Sin accesorios registrados.",
                                style = MaterialTheme.typography.bodySmall,
                                modifier = Modifier.padding(start = 16.dp)
                            )
                        } else {
                            // Itera por cada accesorio

                            pcConAcc.accesorios.forEach { acc ->
                                val textoSerial = if (acc.serial.isNullOrBlank()) "" else " (S/N: ${acc.serial})"
                                Text(
                                    //text = "• ${acc.tipo}: ${acc.descripcion}",
                                    text = "• ${acc.tipo}: ${acc.descripcion}$textoSerial",
                                    style = MaterialTheme.typography.bodySmall,
                                    modifier = Modifier.padding(start = 16.dp)
                                )
                            }
                        }
                        Divider(Modifier.padding(top = 8.dp))
                    }
                }
            }
        }
    }
}
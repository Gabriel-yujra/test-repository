package com.example.control_inventario.ui.viewmodel

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.example.control_inventario.data.db.InventarioDatabase
import com.example.control_inventario.data.db.entity.Accesorio
import com.example.control_inventario.data.db.entity.Computadora
import com.example.control_inventario.data.db.entity.Laboratorio
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharingStarted
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.stateIn
import kotlinx.coroutines.launch

class InventarioViewModel(app: Application) : AndroidViewModel(app) {
    private val dao = InventarioDatabase.getDatabase(app).inventarioDao()

    //Flujo reactivo con el inventario completo
    val inventarioCompleto = dao.getInvetarioCompleto()
        .stateIn(viewModelScope, SharingStarted.Eagerly, emptyList())

    // Estado para edicionde PC
    private val pcsEnEdicion = MutableStateFlow<Computadora?>(null)
    val pcEnEdicion: StateFlow<Computadora?> = pcsEnEdicion.asStateFlow()
    /**
     * Carga una computadora en el estado de edición (para rellenar el formulario).
     */
    fun cargarPcParaEditar(pc: Computadora){
        pcsEnEdicion.value = pc
    }
    /**
     * Limpia el estado de edicion (para limpiar el formulario)
     */
    fun cancelarEdicionPc() {
        pcsEnEdicion.value = null
    }
    // Funciones para interactuar con la BD
    fun agregarLaboratorio(nombre: String, ubicacion: String) = viewModelScope.launch {
        dao.insertLaboratorio(Laboratorio(nombre, ubicacion))
    }

    fun agregarComputadoras(
        codigo: String,
        laboratorioNombre: String,
        marca: String,
        modelo: String,
        cpu: String,
        ramGB: Int,
        estado: String
    ) = viewModelScope.launch {
        dao.insertComputadora(
            Computadora(codigo, laboratorioNombre, marca, modelo, cpu, ramGB, estado)
        )
        // IMPORTANTE: Limpiamos el estado de edicion despues de guardar
        cancelarEdicionPc()
    }
    fun agregarAccesorio(
        computadoraCodigo: String,
        tipo: String,
        descripcion: String,
        serial: String?
    ) = viewModelScope.launch {
        dao.insertAccesorio(
            Accesorio(computadoraCodigo = computadoraCodigo, tipo = tipo, descripcion = descripcion, serial = serial)
        )
    }
    fun eliminarComputadora(codigoPc: String) = viewModelScope.launch {
        dao.deleteComputadoraPorCodigo(codigoPc)
    }
}
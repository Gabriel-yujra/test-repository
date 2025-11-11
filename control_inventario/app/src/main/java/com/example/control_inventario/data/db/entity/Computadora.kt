package com.example.control_inventario.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "computadoras",
    foreignKeys = [
        ForeignKey(
            entity = Laboratorio::class,
            parentColumns = ["nombre"], //PK de laboratio
            childColumns = ["laboratorioNombre"],
            onDelete = ForeignKey.CASCADE // Si se borra el lab, se borran sus PCs
        )
    ],
    indices = [Index("laboratorioNombre")] // Para optimizar consultas
)
data class Computadora(
    @PrimaryKey val codigo: String,
    val laboratorioNombre: String, // Clave foranea
    val marca: String,
    val modelo: String,
    val cpu: String,
    val ramGB: Int,
    val estado: String // Ej: "Operativa", "Mantenimiento"
)
package com.example.control_inventario.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey


@Entity(
    tableName = "accesorios",
    foreignKeys = [
        ForeignKey(
            entity = Computadora::class,
            parentColumns = ["codigo"], // PK dde Computadora
            childColumns = ["computadoraCodigo"], // FK en Accesorio
            onDelete = ForeignKey.CASCADE // Si se borra la PC, se borran sus accesorios
        )
    ],
    indices = [Index("computadoraCodigo")]
)
data class Accesorio(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val computadoraCodigo: String, // Clave foranea
    val tipo: String, // Ej. "Mouse", "teclado", "monitor"
    val descripcion: String, // Ej: "Logitech M185", "Samsung 24 pulgdas"
    val serial: String? // Opcional
)
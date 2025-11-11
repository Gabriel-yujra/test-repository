package com.example.control_inventario.data.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "Laboratorios")
data class Laboratorio (
    @PrimaryKey val nombre: String,
    val ubicacion: String
)
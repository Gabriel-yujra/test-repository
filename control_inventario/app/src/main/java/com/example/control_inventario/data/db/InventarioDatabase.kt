package com.example.control_inventario.data.db

import android.content.Context
import androidx.room.Database
import androidx.room.Room
import androidx.room.RoomDatabase
import com.example.control_inventario.data.db.dao.InventarioDao
import com.example.control_inventario.data.db.entity.Accesorio
import com.example.control_inventario.data.db.entity.Computadora
import com.example.control_inventario.data.db.entity.Laboratorio

@Database(
    entities = [Laboratorio::class, Computadora::class, Accesorio::class],
    version = 1,
    exportSchema = false // Opcional, pero bueno para proctos de ejemplo
)
abstract class InventarioDatabase : RoomDatabase() {
    abstract fun inventarioDao(): InventarioDao
    // Singleton para la instancia de la BD
    companion object {
        @Volatile
        private var INSTANCE: InventarioDatabase? = null

        fun getDatabase(context: Context): InventarioDatabase {
            return INSTANCE ?: synchronized(this) {
                val instance = Room.databaseBuilder(
                    context.applicationContext,
                    InventarioDatabase::class.java,
                    "inventario.db"
                )
                    .fallbackToDestructiveMigration() // util en desarrollo
                    .build()
                INSTANCE = instance
                instance
            }
        }
    }
}
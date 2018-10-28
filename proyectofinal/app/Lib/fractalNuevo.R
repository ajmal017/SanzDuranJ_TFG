#!/usr/bin/env Rscript
setwd('C:/xampp/htdocs/proyectofinal/app/lib/output/')
args <- commandArgs(TRUE)
#install.packages(c("httr", "jsonlite"))
library(quantmod)
library(fractaldim)
#library(tidyverse)
#library(httr)
#library(jsonlite)

zz <- file("all.Rout", open="wt")
sink(zz, type="message")


Sym<-args[1]
fro<-args[2]


x=getSymbols(Sym,src="tiingo", from=fro,api.key="ec521aefb5ccb5202dbf55eb950b19bc1e7a248f")


# Predecimos a partir del endingIndex

endingIndex <-dim(get(x))[1]-101
mainData <- get(x)[,4]
colnames(mainData) <- c("data")
TEST <- mainData[1:endingIndex]
total_error <- 0
error_per_prediction <- c()


#Este es el parámetro correspondiente al método de calculo de las dimensiones fractales
method <- "rodogram"

#Numero de muestras a pintar para cada suposición(guess)
random_sample_count <- 200

Sm <- as.data.frame(TEST, row.names = NULL)

#Hacer 500 predicciones de los siguientes valores de Sm
for(i in 1:50){
  delta <- c()
#Calcular delta entre los valores consecutivos de Sm para usarlos para la construcción de la distribución normal para pintar estimaciones
  
  for(j in 2:length(Sm$data)){
    delta <- rbind(delta, Sm$data[j]-Sm$data[j-1])
  }
  
  # Calcular la desviación estándar de delta
  Std_delta <- apply(delta, 2, sd)
  
  #Actualizar la dimension fractal usada como referencia
  V_Reference <- fd.estimate(Sm$data, method=method, trim=TRUE)$fd
 
  

#Crear 50  estimaciones para pintar de la distribución normal, usando el ultimo valor de Sm como mean y la desviación estándar de delta como desviación estándar. 

  Sm_guesses <- rnorm(random_sample_count , mean=Sm$data[length(Sm$data)], sd=Std_delta )
  
  minDifference = 1000000
  
#Comprobar la dimension fractal de Sm mas cada una de las diferentes estimaciones y elegir el valor con la menor diferencia con la dimension fractal de referencia.
  
  for(j in 1:length(Sm_guesses)){
    new_Sm <- rbind(Sm, Sm_guesses[j])
    new_V_Reference <- fd.estimate(new_Sm$data, method=method, trim=TRUE)$fd
    
    if (abs(new_V_Reference - V_Reference) < minDifference ){      
      Sm_prediction <- Sm_guesses[j]
      minDifference = abs(new_V_Reference - V_Reference)
    }
  }
  
  #Añadir la predicción a Sm
  Sm <- rbind(Sm, Sm_prediction)
  Sm_real <- as.numeric(mainData$data[endingIndex+i])
  error_per_prediction <- rbind(error_per_prediction, (Sm_prediction-Sm_real )/Sm_real )
  total_error <- total_error + ((Sm_prediction-Sm_real )/Sm_real )^2
}

total_error <- sqrt(total_error)
print(total_error)
png(filename="C:/xampp/htdocs/proyectofinal/app/webroot/img/output/outputerror.png", width = 800, height = 600)
plot(error_per_prediction*100, xlab="Prediction Index", ylab="Error (%)")
dev.off()
sink('analysis-output.txt', append=FALSE, type = c("output", "message"))
png(filename="C:/xampp/htdocs/proyectofinal/app/webroot/img/output/outputrodogram.png", width = 800, height = 600)
plot(Sm$data, type="l", xlab="Value Index", ylab="Close", main=Sym)
lines(as.data.frame(mainData$data[1:(endingIndex+50)], row.names = NULL), col="blue")
dev.off()
sink('analysis-output.txt', append=FALSE, type = c("output", "message"))
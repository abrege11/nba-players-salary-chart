from flask import Flask, render_template, request, jsonify
import mysql.connector
import matplotlib.pyplot as plt 
import io, base64
import numpy as np
import matplotlib.ticker as ticker

app = Flask(__name__)

mydb = mysql.connector.connect(host="localhost", user="", password="", database="")

#changed getplot to take an int with the default as 0 to show all values at first
def get_plot(weight_filter=0):
    #changed the query to look for weights greater than or equal to the inputted weight from the html page
    mycursor = mydb.cursor()
    query = "SELECT weight, salary FROM nbamapdata WHERE weight >= %s"
    mycursor.execute(query, (weight_filter,))
    data = mycursor.fetchall()
    
    #normal matplot stuff, pulling data from database
    x, y = [], []  
    for weight, salary in data:
        if weight and salary:
            x.append(weight)
            y.append(float(salary))

    plt.figure(figsize=(10, 6))
    plt.scatter(x, y)
    plt.xlabel('Weight')
    plt.ylabel('Salary')

    #again a few of these lines are from chatGPT to make sure the chart is easy to read
    plt.gca().yaxis.set_major_formatter(ticker.StrMethodFormatter('{x:,.0f}'))
    plt.tight_layout()
    img = io.BytesIO()
    plt.savefig(img, format='png')
    img.seek(0)
    #--------------------------------------------------------------------------

    plot_url = base64.b64encode(img.getvalue()).decode()
    plt.close()
    
    return plot_url

@app.route('/filter_data')
def filter_data():
    min_weight = request.args.get('min_weight', default=0, type=int)
    plot_url = get_plot(min_weight)
    return jsonify({'plot_url': plot_url})

@app.route('/') 
def index(): 
    plot_url = get_plot()
    return render_template('nbachart.html', plot_url=plot_url) 

if __name__ == '__main__':
    app.run('0.0.0.0', port="8060", debug=True)

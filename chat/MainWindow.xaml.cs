using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Sockets;
using System.Net;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace chat
{
    /// <summary>
    /// Logica di interazione per MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            Loaded += Window_Loaded;
        }

        private void Window_Loaded(object sender, RoutedEventArgs e)
        {

        }

            private void Button_Click(object sender, RoutedEventArgs e)
        {
            // Crea un endpoint per il server.
            string serverAddress = "128.116.150.217";
            int serverPort = 10688;
            IPEndPoint serverEndpoint = new IPEndPoint(IPAddress.Parse(serverAddress), serverPort);

            // Crea un socket per la connessione al server.
            Socket clientSocket = new Socket(AddressFamily.InterNetwork, SocketType.Stream, ProtocolType.Tcp);

            // Connette il socket al server.
            clientSocket.Connect(serverEndpoint);

            // Invia un messaggio al server.
            string message = MessageTextBox.Text;
            byte[] messageBuffer = Encoding.ASCII.GetBytes(message);
            clientSocket.Send(messageBuffer);

            // Chiude la connessione al server.
            clientSocket.Shutdown(SocketShutdown.Both);
            clientSocket.Close();
        }
    }
}
